using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace Ecas.Dyn365.Model
{
    public static class ConfigEntity
    {

        internal static class Schema
        {

            public static string ENTITY_NAME = "educ_config";
            public static string STATE_CODE = "statecode";
            public static string GROUP = "educ_group";
            public static string KEY = "educ_key";


        }


        internal class Group
        {
            public static string ORACLE_T4A = "Oracle.T4A";
        }

        internal class Key
        {
            public static string CONNECTION = "ConnectionString";
        }

        public static string ORACLE_COMMAND_TEXT = "ECAS_APP.DYN_ECAS_T4A"; // name of the stored procedure to Upsert the supplier in Oracle T4A

        internal class SPParams
        {
            public static string PARTY_ID = "P_PRTY_ID";
            public static string SUPPLIER_NO = "P_SUPPLIER_NO";
            public static string SITE_NO = "P_SITE_NO";
            public static string STATUS_CODE = "P_STATUS_CODE";
            public static string STATUS_T4A = "P_STATUS_T4A";
            public static string TRANSACTION_CODE = "P_TRANSACTION_CODE";
            public static string TRANSACTION_MESSAGE = "P_TRANSACTION_MESSAGE";


            public static string FIRST_NAME = "p_given_name";
            public static string LAST_NAME = "p_last_name";
            public static string PAYMENT_METHOD = "p_payment_method";
            public static string SIN = "p_sin";
            public static string ADDRESS1 = "p_addr1";
            public static string CITY = "p_city";
            public static string POSTAL_CODE = "p_postal_code";
            public static string PROVINCE_CODE = "p_prv_code";
            public static string COUNTRY_CODE = "p_cnt_code";
            public static string VALID_FROM = "p_valid_from";




        }

    }
}
