using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace Ecas.Dyn365.ECASUpdatesToSupplier.Model
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

        public static string ORACLE_COMMAND_TEXT = "ECAS.DYN_T4A"; // name of the stored procedure to fetch supplier information

        internal class StoredProcedureParams
        {
            public static string PARTY_ID = "P_PRTY_ID";
            public static string SUPPLIER_NO = "P_SUPPLIER_NO";
            public static string SITE_NO = "P_SITE_NO";
            public static string STATUS_CODE = "P_STATUS_CODE";
            public static string STATUS_T4A = "P_STATUS_T4A";
            public static string TRANSACTION_CODE = "P_TRANSACTION_CODE";
            public static string TRANSACTION_MESSAGE = "P_TRANSACTION_MESSAGE";

        }

    }
}
