# Instructions to install SSL certificate
1. Create Secret

* Run the following OC command:

oc -n pvpywj-prod create secret generic workwitheducation.gov.bc.ca-ssl.2020 \
 --from-file=private-key=workwitheducation-gov-bc-ca.key \
 --from-file=certificate=workwitheducation.gov.bc.ca.txt \
 --from-file=csr=workwitheducation-gov-bc-ca.csr \
 --from-file=ca-chain-certificate=L1K-for-certs.txt \
 --from-file=ca-root-certifcate=L1K-root-for-certs.txt
 
2. Create Front-End Route - External Traffic

Use the following settings:

* TLS Termination: Edge
* Insecure Traffic: Redirect

| Route field    | Created secret          | Source file                      |
| -------------- | ----------------------- |--------------------------------- |
| Certificate    | certificate             | cworkwitheducation-gov-bc-ca.txt | 
| Private Key    | private-key             | workwitheducation-gov-bc-ca.key  | 
| CA Certificate | ca-chain-certificate    | L1K-for-certs.txt                | 


3. Verify SSO (KeyCloak) settings - https://sso-dev.pathfinder.gov.bc.ca/

# Deploying

The GitHub Actions workflows deploy using the `*.dc.yaml` templates (Deployments) and `update-configmap.sh`. See the Deploying section in the [main README](../README.md#deploying).
