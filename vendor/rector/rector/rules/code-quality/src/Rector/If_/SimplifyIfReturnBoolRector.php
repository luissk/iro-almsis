<?php

declare(strict_types=1);

namespace Rector\CodeQuality\Rector\If_;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\BinaryOp\NotIdentical;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\Return_;
use Rector\BetterPhpDocParser\Comment\CommentsMerger;
use Rector\CodeQuality\NodeManipulator\ExprBoolCaster;
use Rector\Core\Rector\AbstractRector;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Rector\CodeQuality\Tests\Rector\If_\SimplifyIfReturnBoolRector\SimplifyIfReturnBoolRectorTest
 */
final class SimplifyIfReturnBoolRector extends AbstractRector
{
    /**
     * @var CommentsMerger
     */
    private $commentsMerger;

    /**
     * @var ExprBoolCaster
     */
    private $exprBoolCaster;

    public function __construct(CommentsMerger $commentsMerger, ExprBoolCaster $exprBoolCaster)
    {
        $this->commentsMerger = $commentsMerger;
        $this->exprBoolCaster = $exprBoolCaster;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Shortens if return false/true to direct return',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
if (strpos($docToken->getContent(), "\n") === false) {
    return true;
}

return false;
CODE_SAMPLE
                    ,
                    'return strpos($docToken->getContent(), "\n") === false;'
                ),

            ]);
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [If_::class];
    }

    /**
     * @param If_ $node
     */
    public function refactor(Node $node): ?Node
    {
        if ($this->shouldSkip($node)) {
            return null;
        }

        /** @var Return_ $ifInnerNode */
        $ifInnerNode = $node->stmts[0];

        /** @var Return_ $nextNode */
        $nextNode = $node->getAttribute(AttributeKey::NEXT_NODE);

        /** @var Node $innerIfInnerNode */
        $innerIfInnerNode = $ifInnerNode->expr;

        if ($this->valueResolver->isTrue($innerIfInnerNode)) {
            $newReturnNode = $this->processReturnTrue($node, $nextNode);
        } elseif ($this->valueResolver->isFalse($innerIfInnerNode)) {
            $newReturnNode = $this->processReturnFalse($node, $nextNode);
        } else {
            return null;
        }

        if ($newReturnNode === null) {
            return null;
        }

        $this->commentsMerger->keepComments($newReturnNode, [$node, $ifInnerNode, $nextNode, $newReturnNode]);
        $this->removeNode($nextNode);

        return $newReturnNode;
    }

    private function shouldSkip(If_ $if): bool
    {
        if ($if->elseifs !== []) {
            return true;
        }

        if ($this->isElseSeparatedThenIf($if)) {
            return true;
        }

        if (! $this->isIfWithSingleReturnExpr($if)) {
            return true;
        }

        /** @var Return_ $ifInnerNode */
        $ifInnerNode = $if->stmts[0];

        /** @var Expr $returnedExpr */
        $returnedExpr = $ifInnerNode->expr;

        if (! $this->valueResolver->isTrueOrFalse($returnedExpr)) {
            return true;
        }

        $nextNode = $if->getAttribute(AttributeKey::NEXT_NODE);
        if (! $nextNode instanceof Return_) {
            return true;
        }
        if ($nextNode->expr === null) {
            return true;
        }
        // negate + negate → skip for now
        if (! $this->valueResolver->isFalse($returnedExpr)) {
            return ! $this->valueResolver->isTrueOrFalse($nextNode->expr);
        }
        if (! Strings::contains($this->print($if->cond), '!=')) {
            return ! $this->valueResolver->isTrueOrFalse($nextNode->expr);
        }
        return true;
    }

    private function processReturnTrue(If_ $if, Return_ $nextReturnNode): Return_
    {
        if ($if->cond instanceof BooleanNot && $nextReturnNode->expr !== null && $this->valueResolver->isTrue(
            $nextReturnNode->expr
        )) {
            return new Return_($this->exprBoolCaster->boolCastOrNullCompareIfNeeded($if->cond->expr));
        }

        return new Return_($this->exprBoolCaster->boolCastOrNullCompareIfNeeded($if->cond));
    }

    private function processReturnFalse(If_ $if, Return_ $nextReturnNode): ?Return_
    {
        if ($if->cond instanceof Identical) {
            $notIdentical = new NotIdentical($if->cond->left, $if->cond->right);

            return new Return_($this->exprBoolCaster->boolCastOrNullCompareIfNeeded($notIdentical));
        }

        if ($nextReturnNode->expr === null) {
            return null;
        }

        if (! $this->valueResolver->isTrue($nextReturnNode->expr)) {
            return null;
        }

        if ($if->cond instanceof BooleanNot) {
            return new Return_($this->exprBoolCaster->boolCastOrNullCompareIfNeeded($if->cond->expr));
        }

        return new Return_($this->exprBoolCaster->boolCastOrNullCompareIfNeeded(new BooleanNot($if->cond)));
    }

    /**
     * Matches: "else if"
     */
    private function isElseSeparatedThenIf(If_ $if): bool
    {
        if ($if->else === null) {
            return false;
        }

        if (count($if->else->stmts) !== 1) {
            return false;
        }

        $onlyStmt = $if->else->stmts[0];

        return $onlyStmt instanceof If_;
    }

    private function isIfWithSingleReturnExpr(If_ $if): bool
    {
        if (count($if->stmts) !== 1) {
            return false;
        }

        if ($if->elseifs !== []) {
            return false;
        }

        $ifInnerNode = $if->stmts[0];
        if (! $ifInnerNode instanceof Return_) {
            return false;
        }

        // return must have value
        return $ifInnerNode->expr !== null;
    }
}