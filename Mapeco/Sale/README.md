# Mage2 Module Mapeco Sale

    ``mapeco/module-sale``

 - [Main Functionalities](#markdown-header-main-functionalities)
 - [Installation](#markdown-header-installation)
 - [Configuration](#markdown-header-configuration)
 - [Specifications](#markdown-header-specifications)
 - [Attributes](#markdown-header-attributes)


## Main Functionalities
erp related order and invoice and shipment

## Installation
\* = in production please use the `--keep-generated` option

### Type 1: Zip file

 - Unzip the zip file in `app/code/Mapeco`
 - Enable the module by running `php bin/magento module:enable Mapeco_Sale`
 - Apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`

### Type 2: Composer

 - Make the module available in a composer repository for example:
    - private repository `repo.magento.com`
    - public repository `packagist.org`
    - public github repository as vcs
 - Add the composer repository to the configuration by running `composer config repositories.repo.magento.com composer https://repo.magento.com/`
 - Install the module composer by running `composer require mapeco/module-sale`
 - enable the module by running `php bin/magento module:enable Mapeco_Sale`
 - apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`


## Configuration




## Specifications

 - Controller
	- frontend > erporder/customer/erporder

 - Controller
	- frontend > erpinvoice/customer/erpinvoice

 - Controller
	- frontend > erpshipment/customer/erpshipment

 - Observer
	- customer_register_success > Mapeco\Sale\Observer\Customer\RegisterSuccess

 - Observer
	- customer_customer_authenticated > Mapeco\Sale\Observer\Frontend\Customer\CustomerAuthenticated

 - Plugin
	- afterGetPrice - Magento\Catalog\Model\Product > Mapeco\Sale\Plugin\Frontend\Magento\Catalog\Model\Product


## Attributes



