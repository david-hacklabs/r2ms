INSERT INTO `r2ms`.`risk_template` (`name`, `description`, `impact`, `detail`, `recommendation`, `creation`, `modification`) VALUES ('Business Logic Bypass ','An attacker can circumvent the workflow to disrupt the intended business logic.','Business logic can have security flaws that allow a malicious user to perform actions that are not allowed by the business. An attacker may manipulate these flaws to bypass the intended application workflow. The exploitability depends on the services provided by the application. For example, a malicious user who can set the price of a product on an e-commerce site as a negative number may result in funds being credited to the attacker.
','The application creates insurance policies before it properly validates the payment details. HackLabs “purchased” several insurance policies using several well-known test credit card numbers.

HackLabs noted that the 16 digit test MasterCard number 5105105105105100 was accepted, which created a valid insurance policy number, while the application did not accept the 13-digit Visa number 4222222222222. Further testing to ascertain the exact business logic was not performed.
','Ensure that all user-supplied data is properly validated before business logic is applied. Verify that client requests follow the intended application workflow and that the application enforces the business logic on the server side. 
')

INSERT INTO `r2ms`.`risk_template` (`name`, `description`, `impact`, `detail`, `recommendation`, `creation`, `modification`) VALUES ('Administrative Services with Weak Passwords','Administrative services were found to be set up with weak passwords.','Having administrative accounts configured with weak passwords can lead an attacker to successfully brute forcing the login panel and gain administrative access to the panel. This can ultimately lead to a full web server compromise. 
','HackLabs found that the Apache Tomcat interface located at 150.101.202.74:8080 and available through two domains, respectively cruising.smh.com.au and cruising.theage.com.au, had its admin account set up with the password of “Password123”. 

In less then a few minute of the account brute force attack performed against the login mechanism, HackLabs discovered the admin password and successfully and gained access to the Apache Tomcat Admin Interface.

HackLabs using the compromised server to perform a pivot and test the internal network.

HackLabs considers that further access on the system could have been gained, however the team had to stop further exploitation and analysis due to time restraints.
','HackLabs highly suggests to setup administrator accounts with complex passwords.

In addition, unless really required, HackLabs suggests to restrict access to administrative systems and services, in order to prevent unauthorised access and the ability to initially attack it in the first place.')

INSERT INTO `r2ms`.`risk_template` (`name`, `description`, `impact`, `detail`, `recommendation`, `creation`, `modification`) VALUES ('Vulnerable Third Party Software','Software versions were found to have exploitable vulnerabilities.','Third party software is often used to easily enhance the functionality of a web application; however, vulnerabilities are also commonly found in third party software versions. If the third party does not maintain this software properly, and patches or upgrades are not performed in a timely manner, then these vulnerabilities may be identified by attackers and exploited. Exploits for identified vulnerabilities are often released publicly in vulnerability databases, security advisories, vulnerability scanners, and exploit frameworks. The impact of successfully exploiting identified vulnerabilities is dependent upon the vulnerability instance; however, may range from information disclosure, to application account compromise, right through to full system compromise.
The risk associated with this vulnerability has been adjusted to reflect the highest risk vulnerability that was identified.
','HackLabs was able to identify the following vulnerabilities within third party software that was being used as a part of the web application:
{Software Type and Version}
{Vulnerability Title}
{vulnerability link}

This allows an attacker to then search for specific vulnerabilities and exploits associated with these software versions. HackLabs was able to successfully exploit a number of these vulnerabilities that have been detailed within their corresponding vulnerability sections throughout the report.
This allows an attacker to then search for specific vulnerabilities and exploits associated with these software versions. Since exploiting the underlying infrastructure is outside of the scope of this web application assessment HackLabs did not perform this.
','Vulnerable third party software should have any relevant security patches applied, or upgraded to the latest software version to minimise the number of vulnerabilities that have been identified within the software version. If a fix has not been developed for the identified vulnerabilities then the vendor should be contacted for a solution or a workaround.
It is recommended that a proper patch management and upgrade process be implemented with auditing capabilities to ensure proper patches are installed and updated as necessary.
Solutions to the specific vulnerabilities identified within this penetration test are detailed at the following URLs:

https://hacklabs.com
')

INSERT INTO `r2ms`.`risk_template` (`name`, `description`, `impact`, `detail`, `recommendation`, `creation`, `modification`) VALUES ('Insufficient File Upload Controls ','The application does not sufficiently sanitise uploaded files.','File uploads may be used within web applications for various reasons, such as uploading documents or images. If the application does not perform sufficient input validation to ensure that the uploaded file is in the expected format, an attacker may be able to bypass security checks to upload malicious files to the web server. This functionality is often exploited by attackers to upload backdoors or malicious code onto the system, allowing remote shell access. 
','HackLabs found following file upload feature within the web application. 

•	https://intranet.XYZ.com/Intralogic/KTML4/

This 3rd party application allows for the adding of text and images presumably for adding content within the intranet site. The application allows the uploading of “media” files. It expects a media file to be uploaded and performs a validation check to see if it is appropriate however this file is still uploaded to the server.

Note: This access does not require authentication to the application.

HackLabs then uploaded a “backdoor” file allowing us to interact with the web server. This provides a full suite of tools to explore the server and the network.

HackLabs was able to locate where the file was uploaded to and found that the extension of the file hadn’t changed. This allowed HackLabs to request the file directly causing the code to be executed on the web server. This is shown below:

<Screenshot>
Figure 1 – Backdoor Web Page uploaded to the server.

This backdoor page is live at the following URL:

•	https://intranet.XYZ.com/intralogic/KTML4/uploads/media/ASPspy2.aspx


This allowed HackLabs to confirm that code could be executed on the host. Whilst in the time allowed we could not get a remote shell on the host it was possible to gather files, write folders, upload our own files, Extract passwords (FTP Service Account) and enumerate systems in the internal network.
','Ultimately this is an input validation issue. The web applications should verify that the type of file uploaded is in the expected format. The server should enforce the supported file extension. The file should be renamed and hidden from the attacker. Access to the file should not be accessible directly and should only be supported via a web application interface. Filenames not be included within web application parameter values
')

INSERT INTO `r2ms`.`risk_template` (`name`, `description`, `impact`, `detail`, `recommendation`, `creation`, `modification`) VALUES ('Insecure Direct Object Reference – Account Enumeration ','A direct object reference is when an Application uses the actual name or key of an object when generating web pages. Applications don’t always verify the user is authorised for the target object. This results in an insecure direct object reference flaw. ','As this vulnerability often allows access to sensitive data it can expose the organisation that are running the application to loss of sensitive information and as a consequence regulatory and legal repercussions. NB: Recently University of Sydney were discovered to be exposing sensitive information about students. The Universities application made use of the student number as an identifier. As reported by SMH: http://t.co/g2As59y
','The XXXX application does not properly check access controls when a member is opening a bank account. This allows an authenticated attacker to list all of the members in the database by requesting them during the opening process. The vulnerable page is the ExistingClientDetails.asp page.

This can be performed by using a client side proxy to start the process of opening a bank account. During the process you call a page ExistingClientDetails.asp. If you intercept this request and identify the CLIENTNBR parameter this can be decremented/ incremented to discover other members (Name and Member Number).

Due to the way that XXXX displays the member who is opening the account it is possible to list in a single html page to list the member database. During our testing we scripted this attack and were able to list 2,000 members in less than 30 minutes.

After which they the attacker can complete the process to open accounts in all of the names discovered. 

A video of how this attack is performed is available by clicking on the following and entering the appropriate username and password details:

Figure 2 – Video
','Preventing insecure direct object references requires selecting an approach for protecting each user accessible object (e.g. object number, filename).

Use per user or session indirect object references. This prevents attackers from directly targeting unauthorised resources. For example, instead of using the resources database key, a drop down list of six resources authorised for the current user could use the numbers 1 to 6 to indicate which value the user selected. The application has to map the per-user indirect reference back to the actual database key on the server. OWASP’s ESAPI includes both sequential and random access reference maps that developers can use to eliminate direct object references.
check access. Each use of a direct object reference from an untrusted source must include an access control check to ensure the user is authorised for the requested object.
')

INSERT INTO `r2ms`.`risk_template` (`name`, `description`, `impact`, `detail`, `recommendation`, `creation`, `modification`) VALUES ('Insecure Direct Object Reference','A direct object reference is when an Application uses the actual name or key of an object when generating web pages. Applications don’t always verify the user is authorised for the target object. This results in an insecure direct object reference flaw. ','As this vulnerability often allows access to sensitive data it can expose the organisation running a vulnerable application to loss of sensitive information and as a consequence regulatory and legal repercussions. NB: Recently University of Sydney were discovered to be exposing sensitive information about students. The Universities application made use of the student number as an identifier. As reported by SMH.  http://t.co/g2As59y
','HackLabs found that several resources available through the web service are affected by Insecure Direct Object Reference vulnerabilities. Any user, if authenticated, is in fact able to retrieve other users’ data simply by requesting their respective object identifiers.

HackLabs used a combination of these vulnerabilities and ultimately took advantage of the poor controls implemented by the web service to change a super admin user’s password, log onto the application with his account, exploit an unprotected file upload functionality and obtain a shell on the web server, which gave the team full operative system access.

The next few pages will be a walk through on the steps involved in the exploitation process and will include an illustration/analysis of the single identified vulnerabilities.
 
Insecure Direct Object Reference #1: Retrieve other users’ email addresses

Resource: /eHCAWebService.asmx/GetClientUserDetails
POST CustomerNumber

By either incrementing or decrementing the values sent along with the CustomerNumber parameter, it is possible to retrieve the full names and email addresses of the various clients associated with a customer.
The above screenshot illustrates an attack we ran in which we enumerated several email addresses, which became very useful, as it will be explained in the next pages.
Note that the “AuthenticationToken” supplied is the one associated with the current logged user (gio@hacklabs.com).

Emails enumerated:

•	acumpston@stvincents.com.au	•	mkebsch@stvincents.com.au
•	caroline.oldfield@nursingagency.com.au	•	natasha.seidel@surgeryaustralia.com.au
•	casualnurses@stvincents.com.au	•	nicholas.edwards@surgeryaustralia.com.au
•	don.green@hcagroup.com.au	•	nicole.vass@sesiahs.health.nsw.gov.au
•	flara@stvincents.com.au	•	onesdale@stvincents.com.au
•	gayle.benson@sesiahs.health.nsw.gov.au	•	payroll@healthcareaustralia.com.au
•	gbrown1@stvincents.com.au	•	rriva@stvincents.com.au
•	gcabello@stvincents.com.au	•	rvandergrift@stvincents.com.au
•	helen.keefe@sesiahs.health.nsw.gov.au	•	sallysnook@rocketmail.com
•	hgonzalez@stvincents.com.au	•	sburt@stvincents.com.au
•	kheapy@stvincents.com.au	•	skaichis@stvincents.com.au
•	mjoyce@stvincents.com.au	

Insecure Direct Object Reference #2: Retrieve Mobile Session Data of other users

Resource: /eHCAWebService.asmx/GetActiveMobileSessions
POST UserEmail

Another vulnerable resource identified is the one used to display users’ mobile sessions data. By supplying the email address of a registered user is in fact possible to retrieve its mobile data. Given that at the moment the mobile applications are not in production, only two internal Healthcare Australia employees were found to actually have active sessions (Chris Spall and Don Green).

What makes this resource very interesting from an attacker prospective is the fact that the response also contains the user’s authentication token. Now, by design, this authentication token is all is needed for authentication purposes on almost all resources of the “/mobile” directory, as well as the ones used by the web service. 

In the above illustration it is shown the amount of information returned when Don Green’s email address was supplied.
 
The web service also allows a user to change its password. However, except for the new password, it only requires the authentication token of the user whose password has to be changed.
HackLabs used the authentication token belonging to Don Green to change his password. This is illustrated below: 

At this stage, we logged on the main application with the new password set for Don Green. Being Don a user with high privileges, HackLabs was now able to access a wide range of data and sensitive resources.
 
With the access granted, we browsed the new pages and functionalities now available, and found a page that allowed for the management of Newsletters. The file upload functionality used by the resource was found to not perform security controls on the supplied file, allowing a user to unrestrictedly upload any file type. For the purpose, HackLabs uploaded a web shell.


This allowed the team to execute operative system commands on the webserver, as illustrated in these screenshots.
 ','Preventing insecure direct object references requires selecting an approach for protecting each user accessible object (e.g. object number, filename).

Use per user or session indirect object references. This prevents attackers from directly targeting unauthorised resources. For example, instead of using the resources database key, a drop down list of six resources authorised for the current user could use the numbers 1 to 6 to indicate which value the user selected. The application has to map the per-user indirect reference back to the actual database key on the server. OWASP’s ESAPI includes both sequential and random access reference maps that developers can use to eliminate direct object references.

Check access before granting to end-users. Each use of a direct object reference from an untrusted source must include an access control check to ensure the user is authorised for the requested object.

Finally, the newsletter publication service, although out of scope, does have a file upload vulnerability. Although out of scope, Health Care Australia may wish to address this and incorporate testing of the newsletter service in with future tests.
')

INSERT INTO `r2ms`.`risk_template` (`name`, `description`, `impact`, `detail`, `recommendation`, `creation`, `modification`) VALUES ('Account Enumeration & Secret Questions 
','An attacker is able to enumerate a list of valid usernames and the secret questions for the user.','The application responds with a secret question when a valid user name is submitted. An attacker can then analyse the secret question and tailor the attack appropriately. (i.e. attempt to guess the answer) to the question or to attempt a brute force by shortening a list to place names or colours as an example to significantly reduce the possible.
','HackLabs found that the forgotten password page either returned with a secret question on an error message. The error message indicated if you had provided an invalid username.

The following error message was produced when entering an invalid username in the forgotten password page.

<Screenshot>
Figure 3 – Error message received for an invalid login ID

In contrast, the following figure demonstrates the error message received when a valid login ID is provided:

 
Figure 4 – Secret question displayed for a valid login.

As the error message shows we are able to use this mechanism to discover valid logins. As the application allows users to choose a login name we able to use a dictionary attack against the page to generate a list of valid logins. We know that a login is valid when the page returns with a secret question to answer.
HackLabs performed a brute force attack on this function and ran an attack in which 8834 first names were used as the payload. HackLabs was then able to identify easy targets by reading and guessing the secret question. The attacker could also then run the same style of attack on the answer for the question if required.
 
Figure 5 – an example of the secret questions discovered.

With this attack we enumerated 2550 valid account names. Many hundreds of the secret questions would be trivial to guess and therefore an attacker could learn the Personal Identifiable Information of many of the account holders.
','HackLabs strongly suggests the mechanism only allows to have the change password reset link sent to the account holder rather than having the ability to reset the password on the website after successfully answering the question.

The password recovery feature should not allow multiple and repeat connections. The structure of the application should not allow connection without an additional step through or a Capture mechanism to prevent an automated attack. The page could also make use of “nonce” a one-time URL value to also prevent automated attacks.
')

INSERT INTO `r2ms`.`risk_template` (`name`, `description`, `impact`, `detail`, `recommendation`, `creation`, `modification`) VALUES ('SQL Injection ','SQL injection vulnerabilities arise when user-controllable data is incorporated into database SQL queries in an unsafe manner. An attacker can supply crafted input to break out of the data context in which their input appears and interfere with the structure of the surrounding query.','Various attacks can be delivered via SQL injection, including reading or modifying critical application data, interfering with application logic, escalating privileges within the database and executing operating system commands.
','A number of SQL Injection vulnerabilities were found to affect XYZ 

/facilityscopesearch [name of an arbitrarily supplied request parameter];
/index.php/scopeinfo/ [key parameter];
/index.php?[catid Parameter];
/assessor-development-programs/untrained-assessors;
/departments;
/index.php/facilitiesandlabs/;
/index.php/XYZ-offices;
/XYZ-departments;
/XYZ-offices;
/structure/XYZ-board;
/structure/role-of-aac, and;
/index.php/facilitiesandlabs/.
Proof Of Concept:

Video Tutorial:
https://hacklabs.com/vulnerability-tutorial/2012/8/31/sqli-injection-vulnerability-tutorial.html
Please use the following Credentials - User: ”Videotut” and Password: “&&videotut&&”
','We recommend that a code review be performed to ensure all SQLi vulnerabilities are resolved.

The most effective way to prevent SQL injection attacks is to use parameterised queries (also known as prepared statements) for all database access. 

This method uses two steps to incorporate potentially tainted data into SQL queries: First, the application specifies the structure of the query, leaving placeholders for each item of user input; Second, the application specifies the contents of each placeholder. Because the structure of the query has already defined in the first step, it is not possible for malformed data in the second step to interfere with the query structure. You should review the documentation for your database and application platform to determine the appropriate API’s, which you can use to perform parameterised queries. 

It is strongly recommended that you parameterise every variable data item that is incorporated into database queries, even if it is not obviously tainted, to prevent oversights occurring and avoid vulnerabilities being introduced by changes elsewhere within the code base of the application.

You should be aware that some commonly employed and recommended mitigations for SQL injection vulnerabilities are not always effective:

One common defence is to double up any single quotation marks appearing within user input before incorporating that input into a SQL query. This defence is designed to prevent malformed data from terminating the string in which it is inserted. However, if the data being incorporated into queries is numeric, then the defence may fail, because numeric data may not be encapsulated within quotes, in which case only a space is required to break out of the data context and interfere with the query. 

Further, in second-order SQL injection attacks, data that has been safely escaped when initially inserted into the database is subsequently read from the database and then passed back to it again. Quotation marks that have been doubled up initially will return to their original form when the data is reused, allowing the defence to be bypassed.

Another often cited defence is to use stored procedures for database access. While stored procedures can provide security benefits, they are not guaranteed to prevent SQL injection attacks. The same kinds of vulnerabilities that arise within standard dynamic SQL queries can arise if any SQL is dynamically constructed within stored procedures. Further, even if the procedure is sound, SQL injection can arise if the procedure is invoked in an unsafe manner using user-controllable data.

Ensure all parameters are canonicalised and input validated in the correct locale. 

If dynamic queries are to be used, all data must be appropriately output encoded for the type of SQL server in use. This is still highly dangerous and even one improperly encoded or missing parameter can lead to SQL injection, so HackLabs strongly recommends that dynamic queries are removed completely from the application and prohibited within the organisation. 

Prepared Statements should be used to send precompiled SQL Statements to the backend database, along with the various validated parameters supplied by the user. The database will not interpret the value of the parameters within Prepared Statements, leaving the application immune to SQL Injection vulnerabilities.

The application should use the lowest possible privilege level when accessing the database, removing default database functions that are not needed and applying all relevant database patches.

Consider performing a Secure Code Review, which will find all potential issues within an application. This is particularly useful for high value transactional systems, or applications that have proven difficult to remediate over time. 
')

INSERT INTO `r2ms`.`risk_template` (`name`, `description`, `impact`, `detail`, `recommendation`, `creation`, `modification`) VALUES ('IDS Monitoring & Alerting
','The Intrusions Detection System (IDS) did not detect the attacks that were generated during testing.','Intrusion Detection Systems (IDS) are used to detect attacks on the critical systems. These typically start off with low impact activities and ramp up to full-blown attack and compromise. (Such as the test HackLabs performed for <company name. not including pty ltd> during this exercise). 
','HackLabs used their Penetration Testing methodology to increase the attack intensity to determine at what stage the provider of Network Intrusion Detection Systems (NIDS) and Host-based Intrusion Detection Systems (HIDS) would detect and alert to the attacks.

As these attacks went undetected either IDS is not in place or the current service provider is not performing it correctly.

This would mean that an attacker could successfully carry out attacks and compromise the environment without being detected.
','The IDS systems in place should be confirmed with the provider and should be tested and tuned to ensure they are able to detect and block malicious and focused hacking attempts. 
')


 INSERT INTO `r2ms`.`risk_template` (`name`, `description`, `impact`, `detail`, `recommendation`, `creation`, `modification`) VALUES ('Local File Inclusion (LFI)','A web resource was found to not sanitise user-supplied input before including it with the PHP include functions.','By including and thus executing local files an attacker could be able to execute arbitrary code or retrieve the content of local files.
','HackLabs found two websites to be vulnerable to a known Local File Inclusion vulnerability.
The vulnerability affects the ‘/util/barcode.php’ file at the GET ‘type’ parameter.
A demo is available by clicking on the following links (will show the ‘/etc/passwd’ file contents):

•	https://getonline.aapt.com.au/horde/util/barcode.php?type=../../../../../../../../../../etc/passwd%00
•	http://visptesting.aapt.com.au/util/barcode.php?type=../../../../../../../../../../etc/passwd%00
 ','Ensure that any files containing sensitive information, such as JSP include files, are not accessible by unauthorised users by either configuring ACLs so that these files are not accessible from the Internet, or by moving these files to a location outside of the web server root directory.
')

INSERT INTO `r2ms`.`risk_template` (`name`, `description`, `impact`, `detail`, `recommendation`, `creation`, `modification`) VALUES ('Local File Disclosure','A web resource was found to not sanitise user-supplied input before retrieving its content and displaying it on screen.','An attacker could force the application to display the content of sensitive files, such as application source codes, log files, configuration files and so on. This would potentially lead an attacker to retrieve passwords and sensitive data about the application.
','HackLabs found that the resource “/members/print_subscriptions” does not sanitise user input before sending it to a function that pulls out its content and displays it on the screen. This can be taken advantage of to request arbitrary files and therefore access the content of said file.


The above figure shows HackLabs retrieving the content of the “/etc/passwd” file.

HackLabs then attempted to enumerate files within the “/proc/self/fd” folder in order to find logs and sensitive data that would give the team further insights into the application.

The enumeration attack led the team to discover the file “/proc/self/fd/7”, a log file of over 180mb. Part of its content is displayed in the next page.



From the picture above it can also be seen one of the attacks performed by HackLabs to enumerate files inside /proc/self/fd.

More interesting was the fact the file also contained requests being issued by other users, which disclosed users’ sessions and login tokens, as well as users’ email addresses. HackLabs attempted to use some of these login tokens to gain access to users accounts, and was successful in doing so for several users whose sessions were still valid.

Two figures below show the request containing valid login tokens that allowed the team to login on the different sites with the identity of the users.

 
Logged as Ben on “ultimate-footy.theage.com.au”

 
Logged as Casey Schuliga on “touch.drive.com.au”
 
During the local files enumeration attack, the team successfully found the web document root of the website and this allowed the team access to the application source code, as shown below:

 

Ruby source code of:
/opt/webapps/hagrid/releases/20140306034640/app/controllers/members/members_controller.rb

HackLabs considers that further access on the system and associated websites could have been gained, however the team had to stop further exploitation and analysis due to time restraints.

HackLabs highly recommends further investigation into the way login mechanisms in use by Fairfax sites operate.
','Web applications should implement proper input validation to ensure that parameters cannot be manipulated to point to unauthorised files. 
Implement server-side validation for user input. In this specific case, the application could perform two different checks on user input:
a)	Use a whitelist type of check on user input, so that only specific values (the files the application is expected to receive) are accepted;
b)	Reject trailing slashes and dots (../) since these characters are used to navigate through the file system and access other files;

All application developers should be trained in secure coding practices to ensure all production code is maintained to an organisationally accepted baseline. Install and maintain a strict QA process that can be used to verify and validate coding practices, and to minimise or remove common application vulnerabilities before they are released to production systems.
')

-- XSS