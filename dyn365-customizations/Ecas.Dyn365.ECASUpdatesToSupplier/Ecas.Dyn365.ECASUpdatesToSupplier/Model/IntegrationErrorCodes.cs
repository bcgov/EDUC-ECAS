using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace Ecas.Dyn365.ECASUpdatesToSupplier.Model
{
    public enum IntegrationErrorCodes : int
    {
        FETCH_PAYMENT_RESULTS_FROM_CAS = 10,
        CREATE_SUPPLIER = 20,
        FETCH_SUPPLIER = 30,
        UPDATE_SUPPLIER = 40
    }

    /// <summary>
    /// Extension class to get the integer value from the Enumeration items
    /// </summary>
    static class IntegrationErrorCodesMethods
    {
        public static int GetIntValue(this IntegrationErrorCodes code)
        {
            switch (code)
            {
                case IntegrationErrorCodes.FETCH_PAYMENT_RESULTS_FROM_CAS:
                    return 10;
                case IntegrationErrorCodes.CREATE_SUPPLIER:
                    return 20;
                case IntegrationErrorCodes.FETCH_SUPPLIER:
                    return 30;
                case IntegrationErrorCodes.UPDATE_SUPPLIER:
                    return 40;
                default:
                    return 0;
            }

        }
    }
        
 
}

   
