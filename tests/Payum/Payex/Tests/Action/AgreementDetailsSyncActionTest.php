<?php
namespace Payum\Payex\Tests\Action;

use Payum\Payex\Model\AgreementDetails;
use Payum\PaymentInterface;
use Payum\Request\SyncRequest;
use Payum\Payex\Action\AgreementDetailsSyncAction;
use Payum\Payex\Model\PaymentDetails;

class AgreementDetailsSyncActionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldImplementActionInterface()
    {
        $rc = new \ReflectionClass('Payum\Payex\Action\AgreementDetailsSyncAction');

        $this->assertTrue($rc->isSubclassOf('Payum\Action\PaymentAwareAction'));
    }

    /**
     * @test
     */
    public function couldBeConstructedWithoutAnyArguments()
    {
        new AgreementDetailsSyncAction;
    }

    /**
     * @test
     */
    public function shouldSupportSyncRequestWithArrayAccessAsModelIfOrderIdNotSetAndAgreementRefSet()
    {
        $action = new AgreementDetailsSyncAction();

        $array = $this->getMock('ArrayAccess');
        $array
            ->expects($this->at(0))
            ->method('offsetExists')
            ->with('agreementRef')
            ->will($this->returnValue(true))
        ;
        $array
            ->expects($this->at(1))
            ->method('offsetExists')
            ->with('orderId')
            ->will($this->returnValue(false))
        ;

        $this->assertTrue($action->supports(new SyncRequest($array)));
    }

    /**
     * @test
     */
    public function shouldNotSupportSyncRequestWithArrayAccessAsModelIfOrderIdAndAgreementRefSet()
    {
        $action = new AgreementDetailsSyncAction();

        $array = $this->getMock('ArrayAccess');
        $array
            ->expects($this->at(0))
            ->method('offsetExists')
            ->with('agreementRef')
            ->will($this->returnValue(true))
        ;
        $array
            ->expects($this->at(1))
            ->method('offsetExists')
            ->with('orderId')
            ->will($this->returnValue(true))
        ;

        $this->assertFalse($action->supports(new SyncRequest($array)));
    }

    /**
     * @test
     */
    public function shouldSupportSyncRequestWithAgreementDetailsAsModel()
    {
        $action = new AgreementDetailsSyncAction;

        $this->assertTrue($action->supports(new SyncRequest(new AgreementDetails)));
    }

    /**
     * @test
     */
    public function shouldNotSupportSyncRequestWithPaymentDetailsAsModel()
    {
        $action = new AgreementDetailsSyncAction;

        $this->assertFalse($action->supports(new SyncRequest(new PaymentDetails)));
    }

    /**
     * @test
     */
    public function shouldNotSupportAnythingNotSyncRequest()
    {
        $action = new AgreementDetailsSyncAction;

        $this->assertFalse($action->supports(new \stdClass()));
    }

    /**
     * @test
     */
    public function shouldNotSupportSyncRequestWithNotArrayAccessModel()
    {
        $action = new AgreementDetailsSyncAction;

        $this->assertFalse($action->supports(new SyncRequest(new \stdClass)));
    }

    /**
     * @test
     *
     * @expectedException \Payum\Exception\RequestNotSupportedException
     */
    public function throwIfNotSupportedRequestGivenAsArgumentForExecute()
    {
        $action = new AgreementDetailsSyncAction;

        $action->execute(new \stdClass());
    }

    /**
     * @test
     */
    public function shouldDoSubExecuteCheckAgreementApiRequest()
    {
        $paymentMock = $this->createPaymentMock();
        $paymentMock
            ->expects($this->once())
            ->method('execute')
            ->with($this->isInstanceOf('Payum\Payex\Request\Api\CheckAgreementRequest'))
        ;
        
        $action = new AgreementDetailsSyncAction();
        $action->setPayment($paymentMock);

        $action->execute(new SyncRequest(array(
            'agreementRef' => 'aRef'
        )));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|PaymentInterface
     */
    protected function createPaymentMock()
    {
        return $this->getMock('Payum\PaymentInterface');
    }
}