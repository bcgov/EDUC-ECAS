SonarQube Results:

[![Bugs](https://sonarqube-pvpywj-tools.pathfinder.gov.bc.ca/api/badges/measure?key=ecas&metric=bugs&template=FLAT)](https://sonarqube-pvpywj-tools.pathfinder.gov.bc.ca/dashboard?id=ecas) [![Vulnerabilities](https://sonarqube-pvpywj-tools.pathfinder.gov.bc.ca/api/badges/measure?key=ecas&metric=vulnerabilities&template=FLAT)](https://sonarqube-pvpywj-tools.pathfinder.gov.bc.ca/dashboard?id=ecas) [![Code smells](https://sonarqube-pvpywj-tools.pathfinder.gov.bc.ca/api/badges/measure?key=ecas&metric=code_smells&template=FLAT)](https://sonarqube-pvpywj-tools.pathfinder.gov.bc.ca/dashboard?id=ecas) [![Coverage](https://sonarqube-pvpywj-tools.pathfinder.gov.bc.ca/api/badges/measure?key=ecas&metric=coverage&template=FLAT)](https://sonarqube-pvpywj-tools.pathfinder.gov.bc.ca/dashboard?id=ecas) [![Duplication](https://sonarqube-pvpywj-tools.pathfinder.gov.bc.ca/api/badges/measure?key=ecas&metric=duplicated_lines_density&template=FLAT)](https://sonarqube-pvpywj-tools.pathfinder.gov.bc.ca/dashboard?id=ecas) [![Lines of code](https://sonarqube-pvpywj-tools.pathfinder.gov.bc.ca/api/badges/measure?key=ecas&metric=lines&template=FLAT)](https://sonarqube-pvpywj-tools.pathfinder.gov.bc.ca/dashboard?id=ecas) 

Zap Results:

[![Bugs](https://sonarqube-pvpywj-tools.pathfinder.gov.bc.ca/api/badges/measure?key=ECAS-Zap&metric=bugs&template=FLAT)](https://sonarqube-pvpywj-tools.pathfinder.gov.bc.ca/dashboard?id=ECAS-Zap) [![Vulnerabilities](https://sonarqube-pvpywj-tools.pathfinder.gov.bc.ca/api/badges/measure?key=ECAS-Zap&metric=vulnerabilities&template=FLAT)](https://sonarqube-pvpywj-tools.pathfinder.gov.bc.ca/dashboard?id=ECAS-Zap) [![Code smells](https://sonarqube-pvpywj-tools.pathfinder.gov.bc.ca/api/badges/measure?key=ECAS-Zap&metric=code_smells&template=FLAT)](https://sonarqube-pvpywj-tools.pathfinder.gov.bc.ca/dashboard?id=ECAS-Zap)

# ECAS
Ministry of Education Examinations and Contract Administration System

Repository Map
--------------
- **docker**: Docker compose and manage.sh scripts
- **jenkins**: Jenkins configuration data
- **openshift**: [OpenShift templates](openshift/templates/README.md)
- **sonar-runner**: The component that gets configured to run within SonarQube
- **web-app**: The Laravel & Vue Application
- **web-api**: The .NET Core API layer to Dynamics

## Requirements

- Vue.js (V 2.5)
- Laravel Framework (V 5.8)
- PHP (V 7.1)
- .NET (Core V 2.1)
- OpenShift cluster administered by the “CSI Lab”
- Vue.js files require processing by npm to convert their ECMAscript into vanilla javascript suitable for most browsers. This is currently done manually by the developer and is not built into the automatic deploy process in OpenShift. 

## Project Status

The project is in development. It has been deployed in a DEV environment but has not moved to TEST or PRODUCTION.

The Front End provides a public facing user interface that communicates with a Dynamics instance within the Ministry. The interface is built with Vue.js (V 2.5), it is driven by the Laravel Framework (V 5.8) written in PHP (V 7.1). The Laravel/PHP interface communicates with Dynamics through a ASP.NET WebAPI application running in  .NET (Core V 2.1) (the “Middle Layer”). The Front End and Middle Layer infrastructure operate on the OpenShift cluster administered by the “CSI Lab”.

The Laravel Framework provides a Model-View-Controller design pattern. The main entry point for the application is the DashboardController index method, which loads a single page Vue.js application. The Vue application uses asynchronous calls to interface with the data in Dynamics. These calls are handled by Laravel, also via the DashboardController, which in turn passes them along to the API written in .NET.

Each data entity within Dynamics that the Front End needs to interact with has a corresponding PHP class that extends DynamicsRepository. This parent class contains methods and properties to define a model allowing us to Create, Read, Update, and Destroy Dynamics information. To add functionality to the application new Dynamics entities will be created which will require new PHP classes extending DynamicsRepository. Further documentation is found in the parent class.

### Testing

Testing scripts are in place to run both phpunit test and browser based test via the Dusk Laravel extension. These test scripts are manually run by the developer and are not built into the automatic deploy process in OpenShift. From the web-app root directory:

* ./vendor/bin/phpunit - runs unit tests
* php artisan dusk. - runs Dusk Laravel Extension testing

The Laravel application is set-up to log various actions and code steps, these logs will be helpful for ongoing development and debugging. Laravel creates log files in /web-app/storage/logs

### To Do

At the time of this writing the Team is experiencing connection issues between the .NET API running in OpenShift and the On Premise Dynamics instance running within the Ministry. These issues are firewall / security settings within the Ministry.

**SiteMinder** - Authentication of users to the web portal will be handled by the SiteMinder Web Application. This element has not yet been implemented.

**Slow Loading** - The demo and testing environments experienced significant delays in communicating with Dynamics. Due to the firewall issues mentioned previously communication with the OnPrem instance has yet to be completed. Once that link in the chain is active a more thorough assessment of connection speed between the web portal and Dynamics should be undertaken. If slow response time continues to be an issue the developers may explore several solutions to the issue:
* Caching - simple caching of static data has already been deployed
* Refresh Cache Automatically - A cron routine running on the Server to refresh the cache daily is an approach that would be effective
* Async loading of App - The web portal loads all of it’s data during page load. An A-sync method of data loading is possible reducing page load times
* “Processing” Spinners - No user feedback is provided by the app while it is waiting for data operations to complete, this should be improved

**SonarQube** - As requested by the Ministry, SonarQube testing has been implemented in the deployment pipeline. However, the default test settings are mostly reporting on code that is part of the core frameworks being used on this project (Laravel and Vue). In other words, code that has not been written as part of this project and cannot be altered by this project team. The settings need to be altered to have SonarQube check on only the code that has been written specifically for this project.

**General Design / Accessibility / Mobile Ready** - Initial efforts have been invested in making the web portal design user friendly, accessible, and mobile ready. However, these efforts have not been exhaustive and significant room for improvement remains.

**Failure Handling** - The data connection between the web portal is tested and working. However, the calls to the API are largely assumed to be successful. If a call to the API fails it will do so “quietly”, without providing the user with any useful feedback. API and communication stream should be improved to manage failure conditions.

**Vue working in Internet Explorer!!** - The Internet Explorer web browser is the worst thing to happen to developers, 20 years of awful, and counting! To demonstrate, when viewing the web portal in certain versions of Internet Explorer the user will encounter a blank page. This is an I.E. error when displaying code written in the Vue.js framework. A work around is required to make things work on I.E, as per usual. Additional research will need to be undertaken. This is a small but significant issue. Testing with the I.E. browser should be added to automated testing using the Dusk test suite in Laravel.
