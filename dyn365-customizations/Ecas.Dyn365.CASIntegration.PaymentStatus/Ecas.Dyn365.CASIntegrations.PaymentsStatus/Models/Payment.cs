using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace Ecas.Dyn365.CASIntegrations.PaymentsStatus.Models
{
    /// <summary>
    ///Schema and model class for Payment Entity in ECAS
    /// </summary>
    class Payment
    {
        //Static class to maintain the Active Status Reasons
       public static class ActiveStatus
        {
            public static int STATE_CODE = 0; //Active Status

            public static int FAILED_STATUS_REASON = 610410004;
            public static int CAS_PROCESSING_ERROR_STATUS_REASON = 610410007;
            public static int SENT_TO_CAS_STATUS_REASON = 610410006;
            public static int PROCESSING_STATUS_REASON = 610410000;
            public static int READY_FOR_PROCESSING_STATUS_REASON = 610410001;

        }

        //Static class to maintain the InActive Status Reasons
        public static class InActiveStatus
        {
            public static int STATE_CODE = 1;

            public static int PAYMENT_PROCESSED_STATUS_REASON = 610410008;
        }

        public static class Assignment
        {
            public static string ENTITY_NAME = "educ_assignment";
            public static string RELATED_CONTACT = "educ_contact";
        }

        public static class CAS_AP_CRON_JOB_PROXY
        {
            public static string ENTITY_NAME = "educ_casapcronjobproxy";
            public static string NAME = "educ_name";
        }

        //Schema Names
        public static string PAYMENT_NAME = "educ_name";
        public static string ENTITY_NAME = "educ_payment";
        public static string INVOICE_NUMBER = "educ_invoicenumber";
        public static string PAYMENT_NUMBER = "educ_paymentnumber";
        public static string PAYMENT_STATUS = "educ_paymentstatus";
        public static string PAYMENT_DATE = "educ_paymentdate";
        public static string INVOICE_STATUS = "educ_invoicestatus";
        public static string PAYMENT_ID = "educ_paymentid";



    }
}
