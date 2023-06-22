using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;

namespace Ecas.Dyn365Service.Utils
{
    public class DynamicsAuthenticationSettings
    {
        public string CloudWebApiUrl { get; set; }
        public string CloudResourceUrl { get; set; }
        public string CloudClientId { get; set; }
        public string CloudClientSecret { get; set; }
        public string CloudRedirectUrl { get; set; }
        public string CloudUserName { get; set; }
        public string OnPremWebApiUrl { get; set; }
        public string OnPremUserName { get; set; }
        public string OnPremPassword { get; set; }
        public string OnPremDomain { get; set; }
        public string ActiveEnvironment { get; set; }
        public string TenantId { get; set; }
    }
}
