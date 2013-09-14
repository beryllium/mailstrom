<?php

namespace Beryllium\Mailstrom;

abstract class AbstractMail
{
    public $from;
    public $to;
    public $subject;
    public $message;

    public $responses = array();

    //May want to get rid of this, in case classes need their own implementation
    public function __construct($settings, $client)
    {
        if (is_object($client)) {
            $this->setClient($client);
        }

        if (!empty($settings['from'])) {
            $this->setSender($settings['from']);
        } else {
            throw new \InvalidArgumentException('You must configure a "from" address in your /etc/mailstrom.ini file or specify it on the command line');
        }

        if (!empty($settings['to'])) {
            $this->setRecipient($settings['to']);
        }

        if (!empty($settings['subject'])) {
            $this->setSubject($settings['subject']);
        }

        if (!empty($settings['message'])) {
            $this->setMessage($settings['message']);
        }
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    public function setSender($from)
    {
        $this->from = $from;
        return $this;
    }

    public function setRecipient($to)
    {
        if (false !== strpos($to, ',')) {
            $this->to = explode(',', $to);
        } else {
            $this->to = array($to);
        }
        return $this;
    }

    public function validate()
    {
        $this->validateClient();

        if (empty($this->from)) {
            throw new \InvalidArgumentException('You must specify a sender for this message.');
        } elseif (
            !filter_var($this->from, FILTER_VALIDATE_EMAIL) ||
            // Normally one would do ===false, but we don't want "@example.com" as the email, so a "zero" response should also be determined false
            strpos($this->from, '@') == false
        ) {
            throw new \InvalidArgumentException('You have specified an invalid sender email address.');
        }

        if (empty($this->to) || !is_array($this->to) || count($this->to) == 0) {
            throw new \InvalidArgumentException('You must specify a recipient for this message.');
        } else {
            foreach ($this->to as $email) {
                if (
                    !filter_var($email, FILTER_VALIDATE_EMAIL) ||
                    // Normally one would do ===false, but we don't want "@example.com" as the email
                    // So it is ok that a position of '0' evaluates to false
                    strpos($email, '@') == false
                ) {
                    throw new \InvalidArgumentException('You have specified an invalid recipient email address: ' . $email);
                }
            }
        }

        if (empty($this->subject)) {
            throw new \InvalidArgumentException('You must specify a subject for this message.');
        }
        if (empty($this->message)) {
            throw new \InvalidArgumentException('You must specify a message body for this message.');
        }

        return true;
    }

    abstract public function setClient($client);
    abstract public function getClient();
    abstract public function validateClient();
    abstract public function send();
}
