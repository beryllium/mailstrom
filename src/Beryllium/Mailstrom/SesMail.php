<?php

namespace Beryllium\Mailstrom;

class SesMail
{
    public $_ses;
    public $from;
    public $to;
    public $subject;
    public $message;

    public $responses = array();

    public function __construct($settings, $_ses)
    {
        if (is_object($_ses)) {
            $this->setSes($_ses);
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

    public function setSes($_ses)
    {
        $this->_ses = $_ses;

        return $this;
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
        $this->to = $to;
        return $this;
    }

    public function validate()
    {
        if (empty($this->_ses)) {
            throw new \InvalidArgumentException('You have not configured the Amazon SES service');
        }

        if (empty($this->from)) {
            throw new \InvalidArgumentException('You must specify a sender for this message.');
        } elseif (
            !filter_var($this->from, FILTER_VALIDATE_EMAIL) ||
            // Normally one would do ===false, but we don't want "@example.com" as the email
            strpos($this->from, '@') == false
        ) {
            throw new \InvalidArgumentException('You have specified an invalid sender email address.');
        }

        if (empty($this->to)) {
            throw new \InvalidArgumentException('You must specify a recipient for this message.');
        } elseif (
            !filter_var($this->to, FILTER_VALIDATE_EMAIL) ||
            // Normally one would do ===false, but we don't want "@example.com" as the email
            strpos($this->to, '@') == false
        ) {
            throw new \InvalidArgumentException('You have specified an invalid recipient email address.');
        }

        if (empty($this->subject)) {
            throw new \InvalidArgumentException('You must specify a subject for this message.');
        }
        if (empty($this->message)) {
            throw new \InvalidArgumentException('You must specify a message body for this message.');
        }

        return true;
    }

    public function send()
    {
        $this->validate();

        $this->responses[] = $this->_ses->send_email(
            $this->from,
            array(
                'ToAddresses' => array($this->to),
            ),
            array(
                'Subject.Data' => $this->subject,
                'Body.Text.Data' => $this->message,
            )
        );

        return end($this->responses)->isOK();
    }
}
