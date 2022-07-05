package com.wandertech.wandertreats.general;

import android.app.Activity;
import android.content.Context;
import android.graphics.Bitmap;
import android.util.DisplayMetrics;
import android.view.View;
import android.widget.Toast;

import com.wandertech.wandertreats.PurchaseConfirmationActivity;

/**
 * Created by HP-HP on 3/3/2015.
 */
public class LayoutToImage {

    View _view;
    Context _context;

    Bitmap bMap;

    public LayoutToImage(Context context, View view)
    {
        this._context=context;
        this._view =view;
    }

    public Bitmap convertLayout()
    {

        DisplayMetrics dMetrics = new DisplayMetrics();
        ((PurchaseConfirmationActivity)_context).getWindowManager().getDefaultDisplay().getMetrics(dMetrics);
        int d=dMetrics.densityDpi;

        _view.setDrawingCacheEnabled(true);

        _view.measure(View.MeasureSpec.makeMeasureSpec(0, View.MeasureSpec.UNSPECIFIED), View.MeasureSpec.makeMeasureSpec(0, View.MeasureSpec.UNSPECIFIED));

        _view.layout(0, 0, _view.getMeasuredWidth(), _view.getMeasuredHeight());

        //Toast.makeText(_context, d+"|"+_view.getMeasuredWidth()+"|"+_view.getMeasuredHeight(), Toast.LENGTH_SHORT).show();

        _view.buildDrawingCache(true);

        bMap = Bitmap.createBitmap(_view.getDrawingCache());

        return bMap;
    }
}
