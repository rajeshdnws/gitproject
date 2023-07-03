<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Mapeco\Customer\Controller\Index;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Response\Http;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Request\Http as Request;
use Magento\Framework\App\ResourceConnection;
use Psr\Log\LoggerInterface;

class Emailcheck implements HttpGetActionInterface
{

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var Json
     */
    protected $serializer;
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var Http
     */
    protected $http;

    protected $request;

    /**
     * Constructor
     *
     * @param PageFactory $resultPageFactory
     * @param Json $json
     * @param LoggerInterface $logger
     * @param Http $http
     */
    public function __construct(
        PageFactory $resultPageFactory,
        Json $json,
        LoggerInterface $logger,
        Http $http,
		Request $request,
		ResourceConnection $conn
		
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->serializer = $json;
        $this->logger = $logger;
        $this->http = $http;
	    $this->request = $request;
	    $this->conn = $conn;



    }

    /**
     * Execute view action
     *
     * @return ResultInterface
     */
    public function execute()
    {
          $email = $this->request->getParam('email');
          $connection = $this->conn->getConnection();
		  $vat_id = $this->request->getParam('vat_id');		 
		 
        try {			
	     $contactpersoon = $connection->getTableName('contactpersoon');
		 $lanten = $connection->getTableName('klanten');
	     if(!empty($vat_id))
		 {
			 
	     $query = "Select * FROM " . $lanten." where btw_nummer like '%".$vat_id."%'";
	 
			 
		 }
		  if(!empty($email))
		 {
		 $query = "Select * FROM " . $contactpersoon." where email_adres like '%".$email."%'";
			 
			 
		 }

        //For Select query

		 
         $result = $connection->fetchRow($query);
			if(!empty($result)){

            return $this->jsonResponse(true);
			
		  }else
			
			{
				
		   return $this->jsonResponse(false);
		
			}
        } catch (LocalizedException $e) {
            return $this->jsonResponse($e->getMessage());
        } catch (\Exception $e) {
            $this->logger->critical($e);
            return $this->jsonResponse($e->getMessage());
        }
    }

    /**
     * Create json response
     *
     * @return ResultInterface
     */
    public function jsonResponse($response = '')
    {
        $this->http->getHeaders()->clearHeaders();
        $this->http->setHeader('Content-Type', 'application/json');
        return $this->http->setBody(
            $this->serializer->serialize($response)
        );
    }
}

