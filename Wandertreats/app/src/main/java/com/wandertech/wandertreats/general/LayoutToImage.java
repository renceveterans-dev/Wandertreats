package com.wandertech.wandertreats.general;

import android.content.Context;
import android.graphics.Bitmap;
import android.view.View;

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
        _view.setDrawingCacheEnabled(true);

        _view.measure(View.MeasureSpec.makeMeasureSpec(0, View.MeasureSpec.EXACTLY), View.MeasureSpec.makeMeasureSpec(0, View.MeasureSpec.EXACTLY));

        _view.layout(0, 0, _view.getMeasuredWidth(), _view.getMeasuredHeight());

        _view.buildDrawingCache(true);

        bMap = Bitmap.createBitmap(_view.getDrawingCache());

        return bMap;
    }
}
