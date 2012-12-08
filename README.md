Mailstrom
=========

Mailstrom is a simple command-line script for sending emails via Amazon SES.

(Because configuring sendmail/postfix to do this is *INSANE*)

    ... this can be nothing else than the great whirlpool of the Maelström ...
    ... in the centre of the channel of the Maelström is an abyss penetrating the globe ...
    ... the ordinary accounts of this vortex had by no means prepared me for what I saw ...

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

Configuration
-------------

Mailstrom looks in /etc/mailstrom.ini and ~/.mailstrom.ini for default settings, but anything you specify on the command line takes precedence.

An example configuration file would look like:

    access_key=AAAAAKAKKAKAK
    secret_key=AEAET+/akakak
    from=no-reply@example.com

Usage
-----

This is intended to be somewhat of a replacement for the "mail" command, so you can use it like this:

    $> cat MyFile.txt | ses-mail --to user@example.com --subject "Output of MyFile.txt"

Or you can specify the message as a string:

    $> ses-mail --to user@example.com --subject "Output of MyFile.txt" --message "My email message"

Credits
-------

Built by Kevin Boyd ( http://beryllium.ca | http://github.com/beryllium ) using Amazon's AWS SDK for PHP and GetOptionKit from https://github.com/c9s
