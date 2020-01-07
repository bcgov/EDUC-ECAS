@{
    OrganizationName = 'org6e801ccb'
    ServerUrl = 'https://lclbcannabisdev.crm3.dynamics.com'
    Credential = [PSCredential]::new("serviceuser@bcgovtrial.onmicrosoft.com", ("Cannabis2018" | ConvertTo-SecureString -AsPlainText -Force))
}