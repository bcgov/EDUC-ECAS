using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace Ecas.Dyn365.CASIntegrations.PaymentsStatus.Models
{
    public class Expense
    {
        public static class ActiveStatus
        {
            public static int STATE_CODE = 0; //Active Status
        }

        //Static class to maintain the InActive Status Reasons
        public static class InActiveStatus
        {
            public static int STATE_CODE = 1;

            public static int PAID_STATUS_REASON = 2;
        }

        public static string ENTITY_NAME = "educ_exoense";
        public static string RELATED_PAYMENT = "educ_payment";


    }
}
