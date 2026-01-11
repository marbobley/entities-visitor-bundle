<?php


namespace Marbobley\EntitiesVisitorBundle\Tests\Unit;

use Marbobley\EntitiesVisitorBundle\Domain\ServiceImplementation\PersistVisitorInformation;
use Marbobley\EntitiesVisitorBundle\Domain\ServiceImplementation\ResolverConcreteVisitorInformation;
use Marbobley\EntitiesVisitorBundle\EventListener\VisitorListener;
use Marbobley\EntitiesVisitorBundle\Model\VisitorInformation;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class VisitorListenerTest extends TestCase
{
    private $logger;
    private $persistVisitorInformation;
    private $resolverConcreteVisitorInformation;
    private $listener;

    protected function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->persistVisitorInformation = $this->createMock(PersistVisitorInformation::class);
        $this->resolverConcreteVisitorInformation = $this->createMock(ResolverConcreteVisitorInformation::class);

        $this->listener = new VisitorListener(
            $this->logger,
            $this->persistVisitorInformation,
            $this->resolverConcreteVisitorInformation,
            true
        );
    }

    public function testOnTerminateEventWithNoConcreteEntity()
    {
        $request = new Request();
        $event = new TerminateEvent(
            $this->createMock(HttpKernelInterface::class),
            $request,
            new Response()
        );

        $this->resolverConcreteVisitorInformation->method('resolveConcreteVisitorInformationClass')
            ->willReturn(null);

        $this->logger->expects($this->once())
            ->method('warning')
            ->with($this->stringContains('Aucune entité concrete'));

        $this->persistVisitorInformation->expects($this->never())
            ->method('save');

        $this->listener->onTerminateEvent($event);
    }

    public function testOnTerminateEventWithConcreteEntity()
    {
        $request = new Request([], [], [
            '_controller' => 'App\Controller\TestController::index',
            '_route' => 'test_route'
        ], [], [], [
            'REMOTE_ADDR' => '1.2.3.4',
            'HTTP_USER_AGENT' => 'TestAgent'
        ]);

        $event = new TerminateEvent(
            $this->createMock(HttpKernelInterface::class),
            $request,
            new Response()
        );

        $this->resolverConcreteVisitorInformation->method('resolveConcreteVisitorInformationClass')
            ->willReturn(ConcreteVisitorInformationForListener::class);

        $this->persistVisitorInformation->expects($this->once())
            ->method('save')
            ->with($this->callback(function ($visitor) {
                return $visitor instanceof ConcreteVisitorInformationForListener
                    && $visitor->ip === '1.2.3.4'
                    && $visitor->userAgent === 'TestAgent'
                    && $visitor->controller === 'App\Controller\TestController::index'
                    && $visitor->route === 'test_route';
            }));

        $this->listener->onTerminateEvent($event);
    }

    public function testOnTerminateEventWhenDisabled()
    {
        $listener = new VisitorListener(
            $this->logger,
            $this->persistVisitorInformation,
            $this->resolverConcreteVisitorInformation,
            false
        );

        $request = new Request();
        $event = new TerminateEvent(
            $this->createMock(HttpKernelInterface::class),
            $request,
            new Response()
        );

        $this->logger->expects($this->never())
            ->method('debug');

        $this->persistVisitorInformation->expects($this->never())
            ->method('save');

        $listener->onTerminateEvent($event);
    }
}

class ConcreteVisitorInformationForListener extends VisitorInformation {}
