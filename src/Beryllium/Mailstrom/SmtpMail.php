<?php

namespace Beryllium\Mailstrom;

class SmtpMail extends AbstractMail
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
        if ($this->client instanceof \Swift_Mailer)
            return true;

        return false;
    }

    public function send()
    {
        $this->validate();

        $response = null;
        $result = array('status'=>true);

        try
        {
            $message = \Swift_Message::newInstance()
                ->setSubject($this->subject)
                ->setFrom($this->from)
                ->setTo($this->to)
                ->setBody($this->message);

            $response = $this->getClient()->send($message);
        } catch( \Exception $e )
        {
            $result['status'] = false;
            $result['exception'] = $e;
        }

        if ( $result['status'] == true && (false === $response || 0 === $response ))
        {
            $result['status'] = false;
            $result['exception'] = new \Exception('Failed to send via SMTP');
        }
        else
        {
            $result['response'] = $response;
        }

        $this->responses[] = $result;

        return $result;
    }
}
