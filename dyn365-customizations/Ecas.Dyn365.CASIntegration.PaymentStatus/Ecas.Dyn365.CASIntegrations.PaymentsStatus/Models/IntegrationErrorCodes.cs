using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace Ecas.Dyn365.CASIntegrations.PaymentsStatus.Models
{
    public static class IntegrationErrorCodes
    {
        public static string FETCH_PAYMENT_RESULTS_FROM_CAS = "10";
        public static string CREATE_SUPPLIER = "20";
        public static string FETCH_SUPPLIER = "30";
        public static string UPDATE_SUPPLIER = "40";

        public static int GetIntValueFromCode(string errorCode)
        {
            return int.Parse(errorCode);
        }
    }
}

   
