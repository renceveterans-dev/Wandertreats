package com.wandertech.wandertreats.store.general;

import android.app.AlertDialog;
import android.content.Context;
import android.graphics.Point;
import android.graphics.drawable.ColorDrawable;
import android.view.Display;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.widget.RelativeLayout;

import com.wandertech.wandertreats.store.R;

public class ProgressDialog  extends AlertDialog {
    Boolean isCancellable;
    public ProgressDialog (Context context, Boolean isCancellable) {
        //super(context);
        super(context,R.style.CustomDialogTheme);
//        View view = View.inflate(context, R.layout.dialog_progress, null);
//        setView(view,0,0,0,0);
        //getWindow().setBackgroundDrawable(new ColorDrawable(getContext().getResources().getColor(R.color.transparent)));
        requestWindowFeature(Window.FEATURE_NO_TITLE);


        this.isCancellable = isCancellable;
        setCancelable(isCancellable);
    }



    @Override
    public void show() {
        super.show();

        setContentView(R.layout.dialog_progress);
        WindowManager wm = (WindowManager) getContext().getSystemService(Context.WINDOW_SERVICE);
        Display display = wm.getDefaultDisplay(); // getting the screen size of device
        Point size = new Point();
        display.getSize(size);
        int width = size.x;  // Set your heights
        int height = size.y; // set your widths

        WindowManager.LayoutParams lp = new WindowManager.LayoutParams();
        lp.copyFrom(getWindow().getAttributes());

        lp.width = width;
        lp.height = height;

        getWindow().setAttributes(lp);
//        int width = (int)(getContext().getResources().getDisplayMetrics().widthPixels);
//        int height = (int)(getContext().getResources().getDisplayMetrics().heightPixels);
//
//        getWindow().setLayout(width, height);

        getWindow().clearFlags(WindowManager.LayoutParams.FLAG_DIM_BEHIND);

    }

    public void close(){
        dismiss();
    }
}