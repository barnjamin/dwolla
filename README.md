Dwolla Oauth
============

Be sure to familiarize yourself with the [Dwolla Docs](https://www.dwolla.com/developers/)


Configuration
-------------
Make sure you have an application registered with Dwolla.  They will give you a client ID and client secret, which go at the top of the file. You will also need to change the redirect_uri to the uri you would like your user redirected to after they have approved your application for use.  Change the permissions array to include only the permissions you would like access to.

Use
---

Call the auth_url function to get the url the user needs to be sent to in order to approve the permissions.  Once they sign in on Dwolla and approve the permissions they will be redirected back to whatever you set as your redirect_uri

	$dwolla = new dwolla();
	$url = $dwolla->auth_url();


The user will be redirected to the redirect_uri page with a get parameter of "code=SOMETHING". Call the get_oauth_token function from this page passing in the value for the code GET parameter.  This will return the result from the request and set the oauth_token within the object.  

	$dwolla = new dwolla();
	$dwolla->get_oauth_token($_GET['code']);

Once you have an oauth_token you can make requests with that object.

###Examples

For example if you want to get the users balance you would make a call like this:
	
	$balance = $dwolla->balance();	

To get their contacts:

	$contacts = $dwolla->contacts();

Transactions are a bit different, to send money:

	$reciept = $dwolla->transactions("send", null, $data);

Where $data is an associative array with key/values specified by the table [Here](https://www.dwolla.com/developers/endpoints/transactions/send)


Notes
-----

The oauth_token that you request does not currently have an expiration date so presumably it could be stored and you don't have to request it more than once for any given user.

If you are creating user accounts the password needs to have 8 characters with 1 uppercase, 1 lowercase and 1 number. 

All the data that needs to be sent is passed as an associative array.


If you have any questions or bugs send me a note at ben.guidarelli <at> gmail


License
--------
The MIT License (MIT)
Copyright (c) 2012 Ben Guidarelli

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

