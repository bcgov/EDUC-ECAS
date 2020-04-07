using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace Ecas.Dyn365.CASIntegrations.PaymentsStatus.Models
{
    class CASResponseStatus
    {
        internal class InvoiceStatus
        {
            public static string NOT_VALIDATED = "Never Validated";
            public static string VALIDATED = "Validated";
            public static string NOT_FOUND = "Not Found";
        }

        internal class PaymentStatus
        {
            public static string FULLY_PAID = "Fully Paid";
        }


   

    }
}
