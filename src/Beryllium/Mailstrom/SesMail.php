<?php

namespace Beryllium\Mailstrom;

use Aws\Ses\SesClient;

class SesMail extends AbstractMail
{
    public $client;

    public function setClient($client)
    {
        $this->client = $client;

        return $this;
    }

    public function getClient()
    {
        return $this->client;
    }

    public function validateClient()
    {
        if (!(is_object($this->client) && $this->client instanceof SesClient)) {
            throw new \InvalidArgumentException('You have not configured the Amazon SES service');
        }

        return true;
    }

    public function send()
    {
        $this->validate();

        $result = array('status'=>true);

        try
        {
        $result['response'] = $this->getClient()->sendEmail(
            array(
                'Source' => $this->from,
                'Destination' => array(
                    'ToAddresses' => $this->to,
                ),
                'Message' => array(
                    'Subject' => array('Data' => $this->subject),
                    'Body' => array('Text'=>array('Data' => $this->message)),
                )
        ));
        } catch( \Exception $e )
        {
            $result['status'] = false;
            $result['exception'] = $e;
        }

        $this->responses[] = $result;

        return $result;
    }
}
