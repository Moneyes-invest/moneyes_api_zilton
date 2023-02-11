<?php declare(strict_types = 1);

namespace PHPStan\Type\Doctrine\QueryBuilder\Expr;

use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Broker\Broker;
use PHPStan\Reflection\BrokerAwareExtension;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Rules\Doctrine\ORM\DynamicQueryBuilderArgumentException;
use PHPStan\ShouldNotHappenException;
use PHPStan\Type\Doctrine\ArgumentsProcessor;
use PHPStan\Type\DynamicStaticMethodReturnTypeExtension;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use function class_exists;

class NewExprDynamicReturnTypeExtension implements DynamicStaticMethodReturnTypeExtension, BrokerAwareExtension
{

	/** @var ArgumentsProcessor */
	private $argumentsProcessor;

	/** @var string */
	private $class;

	/** @var Broker */
	private $broker;

	public function __construct(
		ArgumentsProcessor $argumentsProcessor,
		string $class
	)
	{
		$this->argumentsProcessor = $argumentsProcessor;
		$this->class = $class;
	}

	public function setBroker(Broker $broker): void
	{
		$this->broker = $broker;
	}

	public function getClass(): string
	{
		return $this->class;
	}

	public function isStaticMethodSupported(MethodReflection $methodReflection): bool
	{
		return $methodReflection->getName() === '__construct';
	}

	public function getTypeFromStaticMethodCall(MethodReflection $methodReflection, StaticCall $methodCall, Scope $scope): Type
	{
		if (!$methodCall->class instanceof Name) {
			throw new ShouldNotHappenException();
		}

		$className = $scope->resolveName($methodCall->class);
		if (!$this->broker->hasClass($className)) {
			return new ObjectType($className);
		}

		if (!class_exists($className)) {
			return new ObjectType($className);
		}

		try {
			$exprObject = new $className(
				...$this->argumentsProcessor->processArgs(
					$scope,
					$methodReflection->getName(),
					$methodCall->getArgs()
				)
			);
		} catch (DynamicQueryBuilderArgumentException $e) {
			return new ObjectType($this->broker->getClassName($className));
		}

		return new ExprType($className, $exprObject);
	}

}
