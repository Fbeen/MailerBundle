<?php

namespace Fbeen\MailerBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use AppBundle\Entity\Accommodation;

/**
 * Help class to send emails
 *
 * @author Frank Beentjes <frankbeen@gmail.com>
 */
class Mailer
{
    private $container;
    private $message;
    private $template;
    private $data;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->message = \Swift_Message::newInstance();
        $this->template = NULL;
        $this->data = array();
        
        $this->message
            ->setContentType('text/html')
            ->setSubject('testmail')
            ->setFrom($this->container->getParameter('fbeen_mailer.mailaddresses.noreply'))
            ->setTo($this->container->getParameter('fbeen_mailer.mailaddresses.admins'))
            ->setReplyTo($this->container->getParameter('fbeen_mailer.mailaddresses.general'))
            ->setBody('<h1>Testmail</h1>')
        ;
    }
    

    public function getMessage()
    {
        return $this->message;
    }
    
    public function setTo($addresses)
    {
        $this->message->setTo($addresses);
        
        return $this;
    }
    
    public function setSubject($subject)
    {
        $this->message->setSubject($subject);
        
        return $this;
    }
    
    public function setBody($body)
    {
        $this->message->setBody($body);
        
        return $this;
    }
    
    public function setTemplate($filename)
    {
        $this->template = $filename;
        
        return $this;
    }
    
    public function setData($data)
    {
        $this->data = $data;
        
        return $this;
    }
    
    public function renderView($embedImages = FALSE)
    {
        if($this->template)
        {
            return $this->container->get('twig')->render($this->template, $this->mergeData($embedImages));
        }
        
        return $this->message->getBody();
    }
    
    public function sendMail($embedImages = TRUE)
    {
        $this->message->setBody($this->renderView($embedImages));
        
        $mailer = $this->container->get('mailer');
        $mailer->send($this->message); 
        
        $spool = $mailer->getTransport()->getSpool();
        $transport = $this->container->get('swiftmailer.transport.real');

        $spool->flushQueue($transport);
        
        return $this;
    }

    private function mergeData($embed)
    {
        $request = $this->container->get('request_stack')->getCurrentRequest();
        
        $urlHomepage = $request->getUriForPath('/');
        
        $companyName = $this->container->getParameter('fbeen_mailer.company_name');
        $companyLogo = $this->container->getParameter('fbeen_mailer.company_logo');

        if(null !== $companyLogo)
        {
            if(FALSE === filter_var($companyLogo, FILTER_VALIDATE_URL)) 
            {
                $companyLogo = $request->getScheme() . '://' . $request->getHttpHost() . $this->container->get('assets.packages')->getUrl($companyLogo);
            }
            if($embed)  {
                $companyLogo = $this->message->embed(\Swift_Image::fromPath($companyLogo));
            }
        }
        
        return array_merge($this->data, array(
            'companyName' => $companyName,
            'companyLogo' => $companyLogo,
            'urlHomepage' => $urlHomepage,
            'subject'     => $this->message->getSubject()
        ));
    }
}
