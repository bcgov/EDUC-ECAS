# Instructions to install SSL certificate
1. Create Secret
Run the following OC command:

oc -n pvpywj-prod create secret generic workwitheducation.gov.bc.ca-ssl.2020 \
 --from-file=private-key=workwitheducation-gov-bc-ca.key \
 --from-file=certificate=workwitheducation.gov.bc.ca.txt \
 --from-file=csr=workwitheducation-gov-bc-ca.csr \
 --from-file=ca-chain-certificate=L1K-for-certs.txt \
 --from-file=ca-root-certifcate=L1K-root-for-certs.txt
 
2. Create Front-End Route - External Traffic
Use the following settings:

TLS Termination -> Edge
Insecure Traffic -> Redirect
| Route field    | Created secret          | Source file                      |
| -------------- | ----------------------- |--------------------------------- |
| Certificate    | certificate             | cworkwitheducation-gov-bc-ca.txt | 
| Private Key    | private-key             | workwitheducation-gov-bc-ca.key  | 
| CA Certificate | ca-chain-certificate    | L1K-for-certs.txt                | 


3. Verify SSO (KeyCloak) settings - https://sso-dev.pathfinder.gov.bc.ca/

# OpenShift Scripts

These instructions assume you have installed the [OpenShift scripts](https://github.com/BCDevOps/openshift-developer-tools/blob/master/bin/README.md) and that they are accessible via your PATH environment variable.

You will also need to use the [docker/manage.sh](../docker/README.md)  to build the images locally.
# Param management
Run the genParams.sh whenever a new template is created or a change adds a new param
```
genParams.sh
```
Generate pipeline param files whenever you have a pipeline script to test and deploy.
```
genPipelineParams.sh
```

# Build
```
genBuilds.sh     # when you need to add a new build artifact 
genBuilds.sh -u  # when you have want to deploy modified param

```

# Deployment
```
getDepls.sh       # when you need to add a new deployment artifact
getDepls.sh -u    # when you need want to deploy a modified param

```

# Pipeline
Pipeline builds are managed by the build script phase.

