<?php
declare(strict_types=1);

namespace App\Queue\Processor;

use Cake\Core\Configure;
use Cake\Log\LogTrait;
use Cake\ORM\Locator\LocatorAwareTrait;
use Interop\Queue\Context;
use Interop\Queue\Message;
use Interop\Queue\Processor;

class CheckInProcessor implements Processor
{
    use LocatorAwareTrait;
    use LogTrait;

    /**
     * @param \Interop\Queue\Message $message
     * @param \Interop\Queue\Context $context
     * @return string
     */
    public function process(Message $message, Context $context): string
    {
        $data = json_decode($message->getBody(), associative: true);

        $validator = Configure::read('Queue.Validator');
        /** @var \Opis\JsonSchema\ValidationResult $result */
        $result = $validator->validate($data, 'https://greenway.lbdscouts.org.uk/check-in-schema.json');

        if (!$result->isValid()) {
            $this->log(
                message: 'Message Received Not Valid',
                level: LOG_WARNING,
                context: $result,
            );

            return self::REJECT;
        }

        $checkinTable = $this->getTableLocator()->get('CheckIns');

        $participants = $data['participants'];
        $data['participants']['_ids'] = $participants;

        $newCheckIn = $checkinTable->newEntity($data);
        $newCheckIn->set('check_in_time', time());

        if (!$checkinTable->save($newCheckIn)) {
            return self::REJECT;
        }

        return self::ACK;
    }
}
