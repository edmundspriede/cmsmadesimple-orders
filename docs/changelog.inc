<ul>
  <li>Version 1.0.1 - April 2008<br/>
      Initial Public Release
  </li>
  <li>Version 1.1 - September 2008<br/>
      Vast improvement to orders, almost a complete re-design.
  </li>
  <li>Version 1.2 - December 2008<br/>
    <ul>
      <li>This module now handles all talking with taxes and shipping modules.</li>
      <li>Now store order information after the billing information is confirmed. and use it for all other displays.</li>
      <li>Modifications to handle multiple different shipping addresses per order.</li>
      <li>Allow deleting an order (when debug mode is enabled).</li>
      <li>Allow creating a shipping label/invoice on demand for an order in the admin panel, for either printing or email.</li>
      <li>Allow editing billing information for an existing order.</li>
      <li>Now ask the Products module for its Cart module, and then talk to it directly.</li>
      <li>Many other bug fixes, and improvements.</li>
      <li><strong>Notice: Templates and preferences were overwritten</strong><br/>
      Where possible, backups of original templates before overwriting were stored in the temp directory.
        </li>
    </ul>
  </li>
  <li>Version 1.2.1<br/>
      Permits deletion of orders with sufficient permissions and preference setting
  </li>
  <li>Version 1.2.2<br/>
      Minor bug fixes.
  </li>
  <li>Version 1.2.3
     <ul>
       <li>Fixes problem with currency symbol and weight units.</li>
       <li>Fixes problem calculating shipping info.</li>
     </ul>
  </li>
  <li>Version 1.4
    <ul>
       <li>Rework to handle more payment gateways.</li>
       <li>Multiple bug fixes.</li>
       <li>Added the GetOrder method</li>
    </ul>
  </li>
  <li>Version 1.5
    <ul>
       <li>Line items are now stored seperately.</li>
       <li>Added ability for discounts on items.</li>
       <li>Adds ability for different item types.</li>
       <li>Adds ability for a discounts module.</li>
       <li>No longer use session to store any information.</li>
       <li>Calculate discounts before shipping and taxes are calculated.</li>
       <li>Now capable of handling multiple taxes.</li>
    </ul>
  </li>
  <li>Version 1.5
    <ul>
       <li>Completely revamped the templates, though hopefully should still be backwards compatible with older templates for the most part.</li>
       <li>Now handle asyncrhonous payment gateway events.</li>
       <li>Added a new order status</li>
       <li>Fixes many many bugs.</li>
    </ul>
  </li>
  <li>Version 1.5.3
    <ul>
       <li>Fixes minor bug relating to sending the shipping address to the shipping module.</li>
    </ul>
  </li>

  <li>Version 1.6
    <ul>
       <li>The Invoice ID is now stored with each order.</li>
       <li>Adds the invoice_prefix functionality to allow specifying up to a 4 character invoice prefix for each order</li>
       <li>Fixes the required group stuff so that it actually works.</li>
       <li>Adds address mapping from FEU to Orders addresses, so that some of the information from the FEU module can be used to fill in the address field in the billing form.</li>
       <li>Fixes a problem in the invoice page of using the FEU email address instead of the billing form email address.</li>
       <li>Moves all the template editing tabs to their own action, this removes 8 or 9 tabs from the admin panel for people that have all privileges.</li>
    </ul>
  </li>
  <li>Version 1.6.16
    <ul>
      <li>Fixes to the edit link in the confirm order stage.</li>
      <li>Fixes so that shipping costs are calculated after submit of the billing info page.</li>
    </ul>
  </li>
  <li>Version 1.7
    <ul>
      <li>Includes SKU support.</li>
      <li>Now send shipping and billing addresses to the payment gateway modules.</li>
      <li>Now detect wether the payment gateway requires credit card information and automatically ask for it (no need for preferences)</li>
      <li>Adds preferences for default state, and default country</li>
      <li>Improved address handling functionality</li>
    </ul>
  </li>
  <li>Version 1.8
    <ul>
      <li>Adds company to address stuff.</li>
      <li>Now works with CGEcommerceBase.</li>
      <li>Many enhancements and bug fixes.</li>
    </ul>
  </li>
  <li>Version 1.8.3
    <ul>
      <li>Add preference for datastore timeout.</li>
    </ul>
  </li>
  <li>Version 1.8.4
    <ul>
      <li>Improved mechanism for tax calculations... old tax modules should still work (for now).</li>
    </ul>
  </li>
  <li>Version 1.8.5
    <ul>
      <li>Minor template fixes.</li>
      <li>Improvements to credit card number validation.</li>
    </ul>
  </li>
  <li>Version 1.8.5
    <ul>
      <li>Fixes for shipping calculations.</li>
    </ul>
  </li>
  <li>Version 1.8.5
    <ul>
      <li>Adds support for subscription items.</li>
    </ul>
  </li>
  <li>Version 1.9.1
    <ul>
      <li>Adds assocdata capabilities to each order.</li>
      <li>Adds basic reporting mechanism.</li>
      <li>Re-working of payment handling mechanism.</li>
    </ul>
  </li>
  <li>Version 1.10
    <ul>
      <li>Adds assocdata capabilities to line items.</li>
      <li>Now can optionally allow anonymous checkout.</li>
      <li>Fixes bugs in the line item class.</li>
      <li>Minor adjustment to the gateway complete template.</li>
      <li>Changes to the way that encryption keys are generated.</li>
    </ul>
  </li>
  <li>Version 1.10.1
    <ul>
      <li>Fixes broken lang string.</li>
    </ul>
  </li>
  <li>Version 1.10.2
    <ul>
      <li>Now dont generate an error if there are no email addresses selected for admin notification.</li>
      <li>Adds a couple of missing smarty variables to the gateway complete template.</li>
    </ul>
  </li>
  <li>Version 1.10.3
    <ul>
      <li>Fixes problem with orders_helper::is_valid_user()</li>
    </ul>
  </li>
  <li>Version 1.10.4
    <ul>
      <li>Fixes another problem with orders_helper::is_valid_user()</li>
      <li>Fixes minor problem with the EraseCart stuff in the gateway complete action.</li>
    </ul>
  </li>
  <li>Version 1.10.4
    <ul>
     <li>Minor bug fixes.</li>
    </ul>
  </li>
  <li>Version 1.10.6
    <ul>
     <li>Fixes problems with discounts being calculated improperly.</li>
     <li>Fixes error message related to not having an admin email address set.</li>
     <li>Fixes to work with new version of CGEcommerceBase.</li>
     <li>Some minor bug fixes related to E_STRICT and deprecated errors.</li>
    </ul>
  </li>
  <li>Version 1.11
    <ul>
     <li>Adds minor code to allow exporting of some order information to csv.</li>
    </ul>
  </li>
  <li>Version 1.11.1
    <ul>
     <li>Minor bug fixes.</li>
    </ul>
  </li>
  <li>Version 1.11.2
    <ul>
      <li>More memory efficient export (just not as powerful).</li>
    </ul>
  </li>
  <li>Version 1.11.3
    <ul>
      <li>Fixes to email templates (converted from preferences to templates.</li>
      <li>Adds the ability to search for orders with a particular string in the product name.</li>
      <li>Fixes to typos in the default invoice template.</li>
      <li>Minor fix to tab handling in the templates area.</li>
    </ul>
  </li>
  <li>Version 1.11.4
    <ul>
      <li>Fixes to email template stuff (when payment gateways are involved.. I missed this in 1.11.3)</li>
      <li>Fixes to order total display</li>
      <li>Optimizations to shipping cost stuff (dont add 0 value shipping items).</li>
    </ul>
  </li>
  <li>Version 1.11.5
    <ul>
      <li>Important bug fixes.</li>
    </ul>
  </li>
  <li>Version 1.11.5
    <ul>
      <li>Important bug fixes.</li>
    </ul>
  </li>
  <li>Version 1.11.5
    <ul>
      <li>Messaging Improvements</li>
    </ul>
  </li>
  <li>Version 1.12
    <ul>
      <li>Adds option for disabling manual checkout.</li>
      <li>Fixes problems with order status calculations.</li>
      <li>Cleans up template issues.</li>
      <li>Improvements to Manage Order</li>
      <li>Improvements to Manual Payment Processing</li>
      <li>Many Minor Bug Fixes.</li>
    </ul>
  </li>
  <li>Version 1.12.1
    <ul>
      <li>Fix minor error.</li>
    </ul>
  </li>
  <li>Version 1.12.2
    <ul>
      <li>Minor bug fixes for mysql server in E_STRICT.</li>
    </ul>
  </li>
  <li>Version 1.12.3
    <ul>
      <li>Minor bug fixes with add note.</li>
    </ul>
  </li>
  <li>Version 1.13
    <ul>
      <li>Begin work on multiple payment gateway support.</li>
    </ul>
  </li>
  <li>Version 1.13.1
   <ul>
     <li>Minor bug fixes.</li>
   </ul>
  </li>
  <li>Version 1.13.2
   <ul>
     <li>Discounts are now calculated before shipping costs.</li>
     <li>Fixes to templates and stuff that I messed up.</li>
     <li>Minor language changes.</li>
   </ul>
  </li>
  <li>Version 1.13.3
   <ul>
     <li>A minor new option for calculating shipping.</li>
     <li>Minor bug fixes.</li>
   </ul>
  </li>
  <li>Version 1.14
   <p><strong>NOTE: PHP 5.3.x IS REQUIRED.</strong></p>
   <ul>
     <li>Fixes to work with new promotions module.</li>
     <li>Move help out of lang file, some optimizations for CMSMS 1.11.x</li>
     <li>Fixes to work with options instead of attributes.</li>
   </ul>
  </li>
  <li>Version 1.16.x
    <ul>
      <li>Improved billing form template.
       <p><strong>COMPATIBILITY PROBLEM: YOU MUST:</strong><br/>
       a: reset the prototype billing form template to factory defaults<br/>
       b: create a new billing form template<br/>
       c: style the new template.<br/>
       </p>
    </li>
    <li>Improved security and performance.</li>
    <li>Adds support for handling modules.</li>
    <li>Fixes for country and state option handling <em>(1.16.5) - resetting the billing form template again may be necessary.</em></li>
    </ul>
  </li>
  <li>Version 1.17
    <ul>
      <li>Adds support for extra fields to be attached to the order.</li>
      <li>Adds new methods to the shipping and orders objects.</li>
      <li>Cleanup the way addresses are handled in the default form.</li>
      <lI>Ensure that all input parameters from the billing form are stripped and/or cleaned.</li>
    </ul>
  </li>
  <li>Version 1.18
    <br/>
    <div class="warning"><strong>WARNING:</strong> This is a major upgrade.  There may be compatibility problems, primarily with templates.  Though no data should be lost.</li>
    <ul>
      <li>Now require PHP 5.4+</li>
      <li>Works with new payment gateway mechanism.</li>
      <li>Supports multiple payment gateways.</li>
      <li>Improve the way declined and other gateway errors are handled.</li>
      <li>Improvements to handle the way locales that do not use . as a decimal separator are handled.</li>
      <li>add new extra field to orders table and appropriate API changes.</li>
      <li>add new method to test if a line item with a specified sku is somewhere in the order.</li>
      <li>modify (some) default templates to output extra fields.</li>
      <li>add the ability to process extra fields.</li>
      <li>cleanup address processing in the order creation process.</li>
      <li>adds setting notes.</li>
      <li>Improvements for handling multiple carts.</li>
    </ul>
  </li>
  <li>Version 1.19
     <br/>
     <div class="warning"><strong>WARNING:</strong> This is a major upgrade.  There may be compatibility problems.</div>
     <ul>
       <li>Now requires CMSMS 2.1.5 minimum. and CGExtensions 1.54.3, and CGEcommerceBase 1.7</li>
       <li>Now make extensive use of namespaces for classes.  Involved renaming a bunch of classes and fixing numerous errors.  Every class and every file has been touched.</li>
       <li>Adds the ability to store a packing list with each shipment/destination, and to view and print packing lists.  This functionality is only enabled if a Packaging module is installed and configured.</li>
       <li>Handling modules are now processed after shipping modules in the order creation process.</li>
       <li>No longer need to talk to the Products module directly, use the cg_ecomm_productinfo class, which has been enhanced in CGEcommerceBase 1.7</li>
     </ul>
  </li>

  <li>Version 1.23
     <br/>
	<div class="warning"><srong>WARNING:</strong> This is a major upgrade.  There may be compatibility problems.</div>
	<ul>
	<li>Now Requires CMSMS 2.2.2 Minimum.</li>
	<li>Convert all tables to use InnoDB instead of MyISAM.</li>
	<li>Now uses CMSMS 2.2.2 Hooks.</li>
	<li>Major re-factoring of the order building and checkout process.</li>
	<li>New APIs and class methods.</li>
	<li>Destination objects in the database now keep track of (among other things) a source address and a destination address.</li>)
	<li>Now works with shipping policies, box packing modules, and complex shipping modules.</li>
	<li>Now use transactions to delete an order, and send hooks to test if an order can be deleted.</li>
	<li><strong>Note:</strong> The default billing form, and confirmation view templates have changed.  Existing templates may not be compatible.
  	    <p>You should visit the appropriate prototype template type, and reset it to factory defaults.  Then create a new default template of that type.</p>
	</li>
	<li>more...</li>
	</ul>
  </li>

  <li>Version 1.23.1
    <ul>
      <li>Better handling of subscriptions.</li>
      <li>Fix to address handling for a second order in the same session.</li>
      <li>Changes to the default billing form template.</li>
    </ul>
  </li>

  <li>Version 1.23.2
    <ul>
      <li>Adds a new destination status 'Completed'.</li>
    </ul>
  </li>
</ul>
