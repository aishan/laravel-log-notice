<?php
namespace Aishan\LaravelLogNotice\Jobs;

use Aishan\LaravelLogNotice\Service\LogRMQService;
use App\Jobs\Job;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendLogMsgToQueueJob extends Job implements  ShouldQueue
{
    use InteractsWithQueue,SerializesModels;
    public $RMQQueue;
    public $msgBody;
    public $exchange;
    public $exchangeType;
    public $logRemark;
    public $routingKey;

    /**
     * SendMsgToQueueJob constructor.
     * @param $queueName
     * @param $msgBody
     * @param $exchange
     * @param $routingKey
     * @param string $exchangeType
     * @param string $logRemark
     */
    public function __construct($queueName,$msgBody,$exchange,$routingKey,$exchangeType = 'topic',$logRemark = '')
    {
        $this->RMQQueue =  $queueName;
        $this->msgBody = $msgBody;
        $this->exchange = $exchange;
        $this->exchangeType = $exchangeType;
        $this->logRemark = $logRemark;
        $this->routingKey = $routingKey;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $rmqService = new LogRMQService();
        try{
            $rmqService->sendMsg($this->RMQQueue,$this->msgBody,$this->exchange,$this->routingKey,$this->exchangeType);
            if($this->logRemark != 'log'){
                \Log::info('RMQ: '.$this->logRemark.' Data:'.$this->RMQQueue.' '.$this->exchange.' '.$this->routingKey.' '.$this->exchangeType.' '.$this->msgBody);
            }
        }catch(\Exception $e){
            if($this->logRemark == 'log'){
                \Log::debug('RMQ Error:'.$e->getMessage());
            }else{
                \Log::error('RMQ Error:'.$e->getMessage());
            }
        }
    }
}