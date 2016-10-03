# FbeenMailerBundle

This bundle is a tiny layer on top of the Swift Mailer Bundle that is included in each standard Symfony framework

### Features include:

* Mailer helper class
* less configuration
* standard twig layout with optional logo
* easy configuration for used mailaddresses
* method to render example views


## Installation

Using composer:

1) Add `"fbeen/mailerbundle": "dev-master"` to the require section of your composer.json project file.

```
    "require": {
        ...
        "fbeen/mailerbundle": "dev-master"
    },
```

2) run composer update:

    $ composer update

3) Add the bundle to the app/AppKernel.php:
```
        $bundles = array(
            ...
            new Fbeen\MailerBundle\FbeenMailerBundle(),
        );
```

## Configuration

Take a look at this configuration example:
```
fbeen_mailer:
    company_name: "php-programmer.nl"
    company_logo: "https://www.php-programmer.nl/images/logo.png"
    mailaddresses:
        noreply: no-reply@example.com                   # required
        general: info@example.com                       # required
        admins: [admin1@gmail.com, admin2@hotmail.com]  # at least one 

```
If the company_logo is a full URL then it will be used directly. Otherwise The mailer class will use treat it as an asset.

**noreply:** This is the ***From*** that the receiver will see in his email.

**general:** This is the ***Reply-to*** that will be set by default. if the receiver replies than you will receive his mail on this mailaddress

**admins:** This is the ***To*** or the receiver(s) set by default.

## How to use

This can be easily shown with a few examples:

1. to send an email to the admins you don't need to use setTo()
```
/*
 * send an email to the admins
 */
 $this->get('fbeen_mailer')
    ->setSubject('New contact request!')
    ->setTemplate('email/contact_request.html.twig')
    ->setData(array(
        'name' => $name,
        'email' => $email,
        'message' => $message,
     ))
    ->sendMail()
;    
```

2. to send an email to a user you will have to use setTo()
```
/*
 * send an email to the user
 */
 $this->get('fbeen_mailer')
 	->setTo($user->getEmail())
    ->setSubject('Welcome on board!')
    ->setTemplate('email/welcome.html.twig')
    ->setData(array(
        'user' => $user,
     ))
    ->sendMail()
;    
```

3. The mailer class will add addional data to the array before calling twig:
* companyName (the name of your company from the configuration)
* companyLogo (the path or full URL to an image of your choice)
* urlHomepage (the full URL to the "/" path of your website)
* subject     (The subject that has been set by setSubject(); )

So for example 2 you could have a pair of templates as below.

***layout.html.twig:***
```
<table style="font-family: Arial, Helvetica, sans-serif;">
    <tr>
        <td><a href="{{ urlHomepage }}">{% if companyLogo %}<img src="{{ companyLogo }}">{% else %}{{ companyName }}{% endif %}</a></td>
    </tr>
    <tr>
        <td>
            {% block body %}{% endblock %}
        </td>
    </tr>
    <tr>
        <td style="font-size: 12px;">
            Sincerely,<br>
            <br>
            {{ companyName }}
        </td>
    </tr>
</table>
```

***welcome.html.twig:***
```
{% extends 'email/layout.html.twig' %}

{% block body %}
    <table>
        <tr>
            <td><h1>{{ subject }}</h1></td>
        </tr>
        <tr>
            <td>Hi {{ user.name }}, Welcome to our community.</td>
        </tr>
    </table>
{% endblock %}
```