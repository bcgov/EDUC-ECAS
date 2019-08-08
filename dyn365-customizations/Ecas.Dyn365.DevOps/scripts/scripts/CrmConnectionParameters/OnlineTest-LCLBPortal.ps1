@{
    OrganizationName = 'orgbad69190'
    ServerUrl = 'https://lclbcannabistest.crm3.dynamics.com'
    Credential = [PSCredential]::new("serviceuser@bcgovtrial.onmicrosoft.com", ("Cannabis2018" | ConvertTo-SecureString -AsPlainText -Force))
}