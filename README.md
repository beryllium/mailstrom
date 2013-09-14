Mailstrom
=========

Mailstrom is a command-line script for sending emails via SMTP or Amazon SES. 

It behaves similarly to the UNIX `mail` command, in that it can accept an email body via a pipe and then transmit it
over the configured protocol to the specified destination:

    $> echo "Testing" | bin/ses-mail.php -s "My Subject" my.email@example.com

It can also be used interactively to write emails, just like `mail` (press Ctrl+D on a blank line to signal the end of input):

    $> mail -s "My Subject" my.email@example.com
    Dear Sir or Madam,

    I would like to tell you about how awesome the developer of Mailstrom is, and how 
    you should totally follow his blog at http://whateverthing.com/ and perhaps even 
    sign up for his forum-as-a-service offering at http://wtboard.com/

    Sincerely,

    Testy Q. McTesterson
    ^D

Sendmail and Postfix can be a hassle, and are largely overkill for most cloud servers, so with Mailstrom you can save 
yourself the maintenance headaches of maintaining your own mail daemons on your multitude of servers/instances.

---

    ... this can be nothing else than the great whirlpool of the Maelström ...
    ... in the centre of the channel of the Maelström is an abyss penetrating the globe ...
    ... the ordinary accounts of this vortex had by no means prepared me for what I saw ...

---

Installation
------------

Fetch from Github and install dependencies using Composer:

    $> git clone git://github.com/beryllium/mailstrom mailstrom
    $> cd mailstrom
    $> curl -s https://getcomposer.org/installer | php
    $> ./composer.phar install

Configure it to be available in your $PATH:

    $> cd /usr/local/bin
    $> ln -s /path/to/mailstrom/bin/ses-mail.php ses-mail

Or, if you wish, configure it as a drop-in replacement for the "mail" command (presuming it does not currently exist):

    $> cd /usr/local/bin
    $> ln -s /path/to/mailstrom/bin/ses-mail.php mail

Configuration
-------------

Mailstrom looks in /etc/mailstrom.ini and ~/.mailstrom.ini for default settings, but anything you specify on the command line takes precedence.

An example Amazon SES configuration file would look like:

    access_key=AAAAAKAKKAKAK
    secret_key=AEAET+/akakak
    from=no-reply@example.com

Alternatively, an SMTP configuration file would specify a "type" (Amazon SES is assumed to be the default type) as well as SMTP-specific settings:

    type=smtp
    smtp_server=mail.example.com
    smtp_port=25
    smtp_user=username (optional)
    smtp_pass=pass (optional)

Usage
-----

As mentioned above, this is intended to be somewhat of a replacement for the `mail` command, so you can use it like this:

    $> cat MyFile.txt | ses-mail --to user@example.com --subject "Output of MyFile.txt"

Or you can specify the message as a string:

    $> ses-mail --to user@example.com --subject "Output of MyFile.txt" --message "My email message"

Credits
-------

Built by Kevin Boyd ( http://whateverthing.com | http://github.com/beryllium ) using Amazon's AWS SDK for PHP 2, GetOptionKit, and SwiftMailer.

Note: This project is in no way related to the Mailstrom "Inbox Zero" mail client.
