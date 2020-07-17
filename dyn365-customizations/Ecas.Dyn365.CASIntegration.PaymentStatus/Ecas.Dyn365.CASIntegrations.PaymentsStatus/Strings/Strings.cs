using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace Ecas.Dyn365.CASIntegrations.PaymentsStatus.StringConstants
{
    public static class Strings
    {
        public static string INOVICE_NOT_FOUND_TITLE = "The invoice number is missing for the payment record";
        public static string FINISHED_PAYMENTS_FETCH_PROCESSING = "Finished processing the payments record";
        public static string NO_MATCHING_PAYMENTS_FOR_PROCESSING = "No matching paymens for processing, skipping";
        public static string LOADED_CAS_PAYMENT_RESULTS_PLUGIN = "Loaded CASPaymentResultPlugin";
        public static string PLUGIN_DEPTH = "Plugin Depth:";
        public static string LOADED_TARGET_ENTITY = "Loaded Target Entity";
        public static string SINGLETON_RECORD = "Singleton Record - \"DO NOT DELETE THIS RECORD MANUALLY\" ";
        public static string INVOICE_STATUS_NOT_FOUND = "Invoice Not found in CAS";
        public static string INVOICE_NOT_FOUND_DESC = "The given invoice number was not found in CAS and CAS returned *NOT FOUND* in the Invoice Status and" +
                                                        " Payment Status fields in the API response";
        public static string CREATED_SINGLETON_CAS_AP_CRON_JOB = "Created Singleton record for CAS AP Cron Job";
        internal static string NOT_PAID = "Invoice Not Paid";
        internal static string INVOICE_DESC_NOT_PAID = "The Invoice is not paid yet. Check this later.";
        internal static string UNKNOWN_INVOICE_ERROR = "Unidentified invoice error.";
    }
}
