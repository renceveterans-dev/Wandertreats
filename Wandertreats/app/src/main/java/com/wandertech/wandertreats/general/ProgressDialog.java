package com.wandertech.wandertreats.general;

import android.app.AlertDialog;
import android.content.Context;
import android.graphics.drawable.ColorDrawable;
import android.view.WindowManager;

import com.wandertech.wandertreats.R;

public class ProgressDialog  extends AlertDialog {
    Boolean isCancellable;
    public ProgressDialog (Context context, Boolean isCancellable) {
        super(context);
        getWindow().setBackgroundDrawable(new ColorDrawable(android.graphics.Color.TRANSPARENT));
        this.isCancellable = isCancellable;
        setCancelable(isCancellable);
    }



    @Override
    public void show() {
        super.show();
        setContentView(R.layout.dialog_progress);
        getWindow().clearFlags(WindowManager.LayoutParams.FLAG_DIM_BEHIND);
    }

    public void close(){
        dismiss();
    }
}