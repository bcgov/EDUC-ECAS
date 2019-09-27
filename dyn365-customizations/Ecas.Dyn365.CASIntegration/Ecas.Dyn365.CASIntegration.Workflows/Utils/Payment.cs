using Microsoft.Xrm.Sdk;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace Ecas.Dyn365.CASIntegration.Workflows.Utils
{
    public class Payment
    {
        Guid paymentId;
        IOrganizationService organizationService;
        ITracingService tracingService;

        public Payment(Guid _paymentId, IOrganizationService _organizationService, ITracingService _tracingService)
        {
            if (_paymentId == Guid.Empty) throw new ArgumentNullException("Payment Id cannot be null");
            if (_organizationService == null) throw new ArgumentNullException("Organization Service cannot be null");
            if (_tracingService == null) throw new ArgumentNullException("Tracing Service Id cannot be null");

            paymentId = _paymentId;
            organizationService = _organizationService;
            tracingService = _tracingService;

            tracingService.Trace("Loaded Payment Util");
        }

        public PaymentStatusCheckerResult VerifyStatus()
        {
            return new PaymentStatusCheckerResult { Success = true };
        }
    }
}
