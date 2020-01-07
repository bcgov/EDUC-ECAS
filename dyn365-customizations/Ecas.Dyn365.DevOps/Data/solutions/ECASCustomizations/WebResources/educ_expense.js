toggleStatusAndAmountQuantityFields = function () {
    var expenseCode = Xrm.Page.getAttribute('educ_expensecode').getValue();
    var expenseCodeId = '';

    if (expenseCode !== null) expenseCodeId = expenseCode[0].id;
    else return;

    var amountCtrl = Xrm.Page.getControl('educ_amount');
    var quantityCtrl = Xrm.Page.getControl('educ_quantity');
    var statusCtrl = Xrm.Page.getControl('statuscode');
    
    var amount = Xrm.Page.getAttribute('educ_amount');
    var quantity = Xrm.Page.getAttribute('educ_quantity');
    var receiptVerified = Xrm.Page.getAttribute('educ_receiptverified');
    var receiptVerifiedCtrl = Xrm.Page.getControl('educ_receiptverified');


    Xrm.WebApi.retrieveRecord("educ_expensecode", expenseCodeId, "?$select=educ_rate,educ_receiptlimit,educ_receiptrequired").then(
        function success(result) {

            console.log("Expense Code Retrieved values: Receipt Required: " + result.educ_receiptrequired + ", Limit: " + result.educ_receiptlimit + ", Rate: " + result.educ_rate);

            //YES - 610410000
            //Based on Limit - 610410002
            if (result.educ_receiptrequired === 610410000 || 
                (result.educ_receiptrequired === 610410002 && amount.getValue() >= result.educ_receiptlimit)) {
                if (receiptVerified.getValue() !== null) statusCtrl.setDisabled(!receiptVerified.getValue());
                else statusCtrl.setDisabled(true);

                receiptVerifiedCtrl.setVisible(true);

                console.log("Receipt Required");
            }
            else if (result.educ_receiptrequired === 610410001) {
                statusCtrl.setDisabled(false);
                receiptVerifiedCtrl.setVisible(false);

                console.log("Receipt Not Required");
            }
            else{
                statusCtrl.setDisabled(false);
            }

            if (result.educ_rate !== null) {
                //Lock Amount
                amount.setRequiredLevel("none");
                amountCtrl.setDisabled(true);
                
                //Unlock Quantity and Make Quantity required
                quantity.setRequiredLevel("required");
                quantityCtrl.setDisabled(false);
                
                console.log("Rate Based Expense");
            }
            else {
                //Unlock Amount and make it Required
                amount.setRequiredLevel("required");
                amountCtrl.setDisabled(false);
                
                //Default Quantity to 1 and lock field
                quantity.setValue(1);
                quantity.setRequiredLevel("none");
                quantityCtrl.setDisabled(true);

                console.log("Non-rate Based Expense");
            }
        },
        function (error) {
            console.log(error.message);
            // handle error conditions
        }
    );
};

toggleStatusAndAmountQuantityFieldsQuickCreate = function () {
    var expenseCode = Xrm.Page.getAttribute('educ_expensecode').getValue();
    var expenseCodeId = '';

    if (expenseCode !== null) expenseCodeId = expenseCode[0].id;
    else return;

    var amountCtrl = Xrm.Page.getControl('educ_amount');
    var quantityCtrl = Xrm.Page.getControl('educ_quantity');
    
    var amount = Xrm.Page.getAttribute('educ_amount');
    var quantity = Xrm.Page.getAttribute('educ_quantity');


    Xrm.WebApi.retrieveRecord("educ_expensecode", expenseCodeId, "?$select=educ_rate,educ_receiptlimit,educ_receiptrequired").then(
        function success(result) {

            console.log("Expense Code Retrieved values: Receipt Required: " + result.educ_receiptrequired + ", Limit: " + result.educ_receiptlimit + ", Rate: " + result.educ_rate);

            if (result.educ_rate !== null) {
                //Lock Amount
                amount.setRequiredLevel("none");
                amountCtrl.setDisabled(true);
                
                //Unlock Quantity and Make Quantity required
                quantity.setRequiredLevel("required");
                quantityCtrl.setDisabled(false);
                
                console.log("Rate Based Expense");
            }
            else {
                //Unlock Amount and make it Required
                amount.setRequiredLevel("required");
                amountCtrl.setDisabled(false);
                
                //Default Quantity to 1 and lock field
                quantity.setValue(1);
                quantity.setRequiredLevel("none");
                quantityCtrl.setDisabled(true);

                console.log("Non-rate Based Expense");
            }
        },
        function (error) {
            console.log(error.message);
            // handle error conditions
        }
    );
};



recalculateAmount = function () {
    var amountCtrl = Xrm.Page.getControl('educ_amount');
    var quantityCtrl = Xrm.Page.getControl('educ_quantity');
    var statusCtrl = Xrm.Page.getControl('statuscode');
    
    var amount = Xrm.Page.getAttribute('educ_amount');
    var quantity = Xrm.Page.getAttribute('educ_quantity');
    var expenseCode = Xrm.Page.getAttribute('educ_expensecode').getValue();
    var expenseCodeId = '';

    if (expenseCode !== null) expenseCodeId = expenseCode[0].id;
    else return;
    Xrm.WebApi.retrieveRecord("educ_expensecode", expenseCodeId, "?$select=educ_rate,educ_receiptlimit,educ_receiptrequired").then(
        function success(result) {

            console.log("Expense Code Retrieved values: Receipt Required: " + result.educ_receiptrequired + ", Limit: " + result.educ_receiptlimit + ", Rate: " + result.educ_rate);

            if (result.educ_rate !== null) {
                //Set Amount
                //amountCtrl.setDisabled(false);
                amount.setValue(result.educ_rate * quantity.getValue());
                console.log(amount.getValue());
            }
        },
        function (error) {
            console.log(error.message);
            // handle error conditions
        }
    );
}