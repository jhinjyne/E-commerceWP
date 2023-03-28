=== Automated FedEx live/manual rates with shipping labels. ===
Contributors: HITShipo
Tags: fedex, fedex woocommerce, woocommerce fedex, fedex shipping, shipping
Requires at least: 4.0.1
Tested up to: 6.1
Requires PHP: 5.6
Stable tag: 4.0.11
License: GPLv3 or later License
URI: http://www.gnu.org/licenses/gpl-3.0.html

FedEx shipping plugin, Integrate the FedEx for Domestic and international Shipping. According to the destination, We are Providing all kind of FedEx Services.

== Description ==

FedEx shipping plugin, Integrate the FedEx for Domestic and international Shipping. According to the destination, We are Providing all kind of FedEx Services.

Calculate shipping costs in real time through the FedEx's online quote, based on the products of the cart and the postal codes of origin and destination. It also shows the estimated delivery times based on the moment of calculation.

> Trusted shipping costs obtained directly from FedEx, with different variants or types of services available.
> In case your products do not have dimensions or weight, the module allows you to set dimensions and weight of customized products to calculate the shipping costs.
> Option to add an impact or adjustment to the shipping costs displayed by the carrier before showing it to the customer.
> Improve the shopping experience by allowing you to show estimated delivery times.
> Simple configuration focused on obtaining accurate and adjusted shipping costs for your business model.
> At the moment, no type of contract with the provider is required to make use of this module.
> No override files are required for proper functionality of the module.

= Our Guarantees =

* All our developments are validated by A2Z Group Team.
* Support warranty in the plugin's bugs.
* We can customize the module or make the necessary modifications. Contact us to request an estimate.

= Features =

* Fedex shipping plugin supports SOAP API
* Fedex shipping plugin supports REST API
* Calculates shipping costs in real time through the FedEx online quote service.
* Use of cache in the results to improve the loading speed of the site and to make calculation of the costs only when there is a change in the shopping cart.
* Shipping costs based 100% on the zip code.
* Compatible with orders generated directly from the Back Office.
* Option to perform shipping cost calculations only on the order confirmation page to improve site navigation performance.
* Option to specify the post code of origin for shipments.
* The Shipping cost can be determined in 2 different ways:
	1). Based on weight and cubic volume.
	2). Based on weight and an optimized three-dimensional volume, which simulates the packaging of all products in a 3D box.
* Option to set custom dimensions for your products to be used when calculating shipping costs.
* Option to establish a custom weight for your products to be used when calculating shipping costs.
* Option that allows you to establish an impact on the cost of the shipment before showing it to the customer.
* Option to show estimated delivery times at checkout in each of the available services, with multiple customization options that will allow you to provide even more accurate estimated times.
* Multi-currency compatible.
* Multi-store compatible.
* Full and detailed documentation.

= FAQ's =

Q). Do I need credentials provided by FedEx to use the module?
A). Yes, this module makes the estimates directly from the FedEx's online Account.

Q). Does it work with other currencies?
A). Yes, Its done by hooks.

Q). Are the prices updated?
A). Yes, costs are obtained directly from FedEx's online quote in real time.

Q). Is it compatible with other carrier modules?
A). Yes, it is completely compatible.

= What your customers will like =

* Knowing the cost of shipping in real time based on the products of the shopping cart and the delivery address, as well as knowing at the time the estimated delivery times, undoubtedly generates greater confidence to the customer and helps closing the sale.
* Reduce communication time with the store manager.
* It has a greater list of options for shipping services.

= Recommendation =

* It is mandatory that your products have specified dimensions and weight in the "Shipping" tab within the product edition, or that you use the module options to consider these custom values ​​at the time of calculation.
* The quotation system only works for addresses in Anywhere.
* The quotes are based on postal codes, it is mandatory to have this option active in your country settings.

= Useful filters =

1) Filter to adjust shipping cost

> add_filter('hitstacks_fedex_shipping_cost_conversion','fedex_shipping_cost_conversion',10,1);
> function fedex_shipping_cost_conversion($ship_cost){
> 	return $ship_cost+1000;
> }

2) Filter to set Flat rate

> function fedex_shipping_cost_conversion($ship_cost, $pack_weight = 0, $to_country = '', $rate_code = ''){
>	$sample_flat_rates = array("GB"=>array(		//Use ISO 3166-1 alpha-2 as country code
>								"weight_from" => 10,
>								"weight_upto" => 30,
>								"rate" => 2000,
>								"rate_code" => "INTERNATIONAL_FIRST",		//You can add fedex service type here. Get this from Fedex Dev docs - Appendix -> Service Types
>								),
>							"US"=>array(
>								"weight_from" => 10,
>								"weight_upto" => 30,
>								"rate" => 5000,
>								),
>							);
>
>	if(!empty($to_country) && !empty($sample_flat_rates)){
>		if(isset($sample_flat_rates[$to_country]) && ($pack_weight >= $sample_flat_rates[$to_country]['weight_from']) && ($pack_weight <= $sample_flat_rates[$to_country]['weight_upto'])){
>			$flat_rate = $sample_flat_rates[$to_country]['rate'];
>			return $flat_rate;
>		}else{
>			return $ship_cost;
>		}
>	}else{
>		return $ship_cost;
>	}
>
> }
> add_filter('hitstacks_fedex_shipping_cost_conversion','fedex_shipping_cost_conversion',10,4);

(Note: Flat rate filter example code will set flat rate for all fedex carriers. Have to add code to check and alter rate for specific carrier.
 While copy paste the code from worpress plugin page may throw error "Undefined constant". It can be fixed by replacing backtick (`) to apostrophe (') inside add_filter())

= About FedEx =

FedEx Corporation is an American multinational courier delivery services company headquartered in Memphis, Tennessee. The name "FedEx" is a syllabic abbreviation of the name of the company's original air division, Federal Express, which was used from 1973 until 2000.

= About [HITShipo](https://hitshipo.com/) =

We are Web Development Company in France. We are planning for High Quality WordPress, Woocommerce, Edd Downloads Plugins. We are launched on 4th Nov 2018.

= What a2Z Plugins Group Tell to Customers? =

> "Make Your Shop With Smile"

== Screenshots ==
1. Fedex integration settings.
2. Configure Shipper Address.
3. Configure packing algorithm.
4. FedEx Shipping Rates Confguration.
5. Shipping label configuration.
6. Live rates in cart page.
7. Order placed via Fedex carrier.
8. Create manual label/view label on edit order page.

== Changelog ==

= 4.0.11 =
*Release Date - 24 February 2023*
	>minor changes

= 4.0.10 =
*Release Date - 20 February 2023*
	>minor bug fixes
= 4.0.9 =
*Release Date - 20 February 2023*
	>Updated meeting link

= 4.0.8 =
*Release Date - 07 February 2023*
	>Added Duty settings on order page for return shipments

= 4.0.7 =
*Release Date - 06 February 2023*
	>Fixed return shipment issue

= 4.0.6 =
*Release Date - 01 February 2023*
	>Minor bugfix

= 4.0.5 =
*Release Date - 25 January 2023*
	>Minor improvements

= 4.0.4 =
*Release Date - 2 January 2023*
	>Minor bugfix

= 4.0.3 =
*Release Date - 28 December 2022*
	>Minor improvements

= 4.0.2 =
*Release Date - 24 November 2022*
	>Minor improvements
	
= 4.0.1 =
*Release Date - 17 November 2022*
	>update tested version

= 4.0.0 =
*Release Date - 09 November 2022*
	>Added REST API support

= 3.3.11 =
*Release Date - 04 october 2022*
	>minor bugfix

= 3.3.10 =
*Release Date - 20 September 2022*
	>Added Duty payment option

= 3.3.9 =
*Release Date - 19 September 2022*
	>Added option to include tax in rate

= 3.3.8 =
*Release Date - 15 September 2022*
	>Minor improvements

= 3.3.7 =
*Release Date - 12 August 2022*
	>Add new button,plugin name change

= 3.3.6 =
*Release Date - 05 August 2022*
	>minor bug fix

= 3.3.5 =
*Release Date - 29 july 2022*
	>minor bug fix

= 3.3.4 =
*Release Date - 29 july 2022*
	>minor bug fix

= 3.3.3 =
*Release Date - 28 july 2022*
	>Intergration field add

= 3.3.2 =
*Release Date - 20 july 2022*
	>add new button

= 3.3.1 =
*Release Date - 20 july 2022*
	> Minor bug fix

= 3.3.0 =
*Release Date - 19 july 2022*
	> SHIPPING LABEL AUTOMATION

= 3.2.3 =
*Release Date - 14 july 2022*
	> Account rate bug fix

= 3.2.2 =
*Release Date - 23 june 2022*
	> Minor bug fix

= 3.2.1 =
*Release Date - 10 june 2022*
	> Minor bug fix


= 3.2.0 =
*Release Date - 06 june 2022*
	>Added New Password Field

= 3.1.9 =
*Release Date - 22 March 2022*
	> Minor bug fix

= 3.1.8 =
*Release Date - 22 March 2022*
	> Undefined Variable rate fix

= 3.1.7 =
*Release Date - 18 March 2022*
	> Minor bug fix

= 3.1.6 =
*Release Date - 29 January 2022*
	> Some Services Names Changed

= 3.1.5 =
*Release Date - 8 December 2021*
	> Rating updated to v31

= 3.1.4 =
*Release Date - 28 Novemeber 2021*
	> DHS,AED Currency fix

= 3.1.3 =
*Release Date - 24 Novemeber 2021*
	> Added DHS Currency

= 3.1.2 =
*Release Date - 29 July 2021*
	> Fixed "POST" issue on order page on some sites

= 3.1.1 =
*Release Date - 27 July 2021*
	> Added SFR currency

= 3.1.0 =
*Release Date - 22 July 2021*
	> Return label integration

= 3.0.6 =
*Release Date - 15 July 2021*
	> Wordpress version updated

= 3.0.5 =
*Release Date - 06 July 2021*
	> Added option to send fedex pack type on rates same as shipping label settings.

= 3.0.4 =
*Release Date - 08 May 2021*
	> Updated rates API.

= 3.0.3 =
*Release Date - 31 March 2021*
	> Fixed data collapse by product name.

= 3.0.2 =
*Release Date - 17 March 2021*
	> Added box packing.

= 3.0.1 =
*Release Date - 16 March 2021*
	> Fixed issue "Site not linked with HITShipo".

= 3.0.0 =
*Release Date - 13 March 2021*
	> API, UI Updated.

= 2.5.15 =
*Release Date - 12 March 2020*
	> Added destination zip code on filter.

= 2.5.14 =
*Release Date - 24 February 2020*
	> Fixed dimension not sending.

= 2.5.13 =
*Release Date - 24 Dec 2020*
	> Fixed some bugs with Multi-Vendor.

= 2.5.12 =
*Release Date - 17 Dec 2020*
	> Minor bugs fixes.

= 2.5.11 =
*Release Date - 17 Dec 2020*
	> Minor bugs fixes.

= 2.5.10 =
*Release Date - 12 Dec 2020*
	> Minor bugs fixes.

= 2.5.9 =
*Release Date - 30 Nov 2020*
	> Minor bugs fixes.

= 2.5.8 =
*Release Date - 30 Nov 2020*
	> Manual rates hook added.

= 2.5.7 =
*Release Date - 24 Nov 2020*
	> Minor bugs fixes.

= 2.5.6 =
*Release Date - 30 Oct 2020*
	> Removed parent credentials on rate request.

= 2.5.5 =
*Release Date - 29 Oct 2020*
	> Added filter to set flat rate.

= 2.5.4 =
*Release Date - 26 Oct 2020*
	> Minor bugs fixes.

= 2.5.3 =
*Release Date - 25 Oct 2020*
	> Minor bugs fixed.

= 2.5.2 =
*Release Date - 23 Oct 2020*
	> Minor bugs fixed.

= 2.5.1 =
*Release Date - 22 Oct 2020*
	> Minor bugs fixed.

= 2.5.0 =
*Release Date - 19 Oct 2020*
	> Added option to exclude country.

= 2.4.1 =
*Release Date - 29 Aug 2020*
	> Added filter to adjust shipping cost.

= 2.4.0 =
*Release Date - 28 Aug 2020*
	> Introduced COD option.

= 2.3.3 =
*Release Date - 11 Aug 2020*
	> Added "NMP" in fedex currency list.

= 2.3.2 =
*Release Date - 08 Aug 2020*
	> Added option to choose fedex currency.

= 2.3.1 =
*Release Date - 07 Aug 2020*
	> Minor bugs fixed.

= 2.3.0 =
*Release Date - 06 Aug 2020*
	> Introduced currency conversion on checkout.

= 2.2.5 =
*Release Date - 01 Aug 2020*
	> Contact Updated.

= 2.2.4 =
*Release Date - 21 July 2020*
	> Fixed IND to IND shipment.

= 2.2.3 =
*Release Date - 10 July 2020*
	> Fixed Some Known Bugs.

= 2.2.2 =
*Release Date - 04 July 2020*
	> Removed some label types causing issues while generating label.

= 2.2.1 =
*Release Date - 29 May 2020*
	> Fixed checkout page loading issue.

= 2.2.0 =
*Release Date - 28 May 2020*
	> Now support Multi-Vendor.

= 2.1.5 =
*Release Date - 09 May 2020*
	> Fixed showing "Undefined" while adding products not having weights to cart.

= 2.1.4 =
*Release Date - 08 May 2020*
	> Minor bugs fixed.

= 2.1.3 =
*Release Date - 08 May 2020*
	> Parent weight and dimensions of variable products will be utilized when they are not set for its variations.
	> Sending shipping cost to shipo on manual label generation.

= 2.1.2 =
*Release Date - 02 May 2020*
	> Weight based rate calulation added

= 2.1.1 =
*Release Date - 25 April 2020*
	> Minor bugs fixed

= 2.1.0 =
*Release Date - 24 April 2020*
	> One Rate feature added

= 2.0.3 =
*Release Date - 22 April 2020*
	> send shipping cost to shipo

= 2.0.2 =
*Release Date - 17 April 2020*
	> weight and dim conversion

= 2.0.1 =
*Release Date - 10 March 2020*
	> Minor bugs fixed

= 2.0.0 =
*Release Date - 07 March 2020*
	> Initial Version Compatibility with HITShipo
= 1.0.0 =
*Release Date - 06 November 2018*
	> Initial Version
