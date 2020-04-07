using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using Microsoft.Xrm.Sdk;

namespace Ecas.Dyn365.ECASUpdatesToSupplier.Model
{
    internal class Strings
    {

        public static string SINGLETON_RECORD = "Singleton Record - \"DO NOT DELETE THIS RECORD MANUALLY\" ";
        public static string PARTY_ID_MISSING = "Party ID is missing from the contact record";
        public static string PARTY_ID_MISSING_DESCRIPTION = "Could not find Party ID in the contact record ";
        public static string UNABLE_TO_FETCH_CONTACT_RECORDS = "Unable to fetch the \"New CAS User\" or the \"Update Requested\" " +
                                                                   "Contact records or no contact records found with Supplier Status = New CAS User OR Update Requested";
        public static string UNABLE_TO_FETCH_PAYMENT_RECORDS = "Unable to fetch payment records. ";
        internal static string CONFIGURATION_NOT_FOUND = "System Configuration for Group '{0}', Key '{1}' doesn't exist..";
        internal static string STORED_PROCEDURE_EXCEPTION = "Something went wrong while tyring to run the Stored procedure-";
        internal static string LOADED_UPDATE_SUPPLIER_PLUGIN = "Loading Successfull: SendSuppliertoOraclePlugin";
        internal static string PLUGIN_DEPTH = "Plugin Depth:";
        internal static string TARGET = "Target";
        internal static string UPDATE_SUPPLIER_PLUGIN_ERROR_OCCURED = "An error occurred in SendSuppliertoOraclePlugin.";
        internal static string UPDATE_SUPPLIER_PLUGIN_SHORT_ERROR = "SendSuppliertoOraclePlugin Error: {0}";
        internal static string T4A_FER_ERROR = "T4A returned FER";

    }


}
