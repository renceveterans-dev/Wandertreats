package com.wandertech.wandertreats.general;

import android.app.Activity;
import android.app.AlertDialog;
import android.content.Context;
import android.graphics.Bitmap;
import android.graphics.Color;
import android.graphics.drawable.ColorDrawable;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewTreeObserver;
import android.widget.ImageView;
import android.widget.LinearLayout;

import com.bumptech.glide.Glide;
import com.bumptech.glide.request.target.SimpleTarget;
import com.bumptech.glide.request.transition.Transition;
import com.google.android.material.button.MaterialButton;
import com.squareup.picasso.Picasso;
import com.wandertech.wandertreats.R;
import com.wandertech.wandertreats.utils.Utils;

import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.appcompat.widget.AppCompatImageView;
import androidx.appcompat.widget.AppCompatTextView;
import androidx.appcompat.widget.LinearLayoutCompat;

public class ImagePreviewDialog {
    private Boolean isCancellable;
    private Context mContext;
    private AlertDialog.Builder builder;
    private LayoutInflater inflater;
    private View dialog;
    private ImageView preview;
    private View seperator4;
    private AlertDialog alert;

    public ImagePreviewDialog (Context context) {

        this.mContext =context;
        this.builder = new AlertDialog.Builder(context);
        this.inflater = (LayoutInflater) context.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
        this.dialog = inflater.inflate(R.layout.dialog_image_preview, null);



        preview = (ImageView) this.dialog.findViewById(R.id.imagePreview);



        preview.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                dismiss();
            }
        });

        this.builder.setView(dialog);
        this.alert = this.builder.create();
        this.alert.setCancelable(true);
        this.alert.getWindow().setBackgroundDrawable(new ColorDrawable(Color.TRANSPARENT));

    }

    public void createPreview(String url){

        ViewTreeObserver vto = preview .getViewTreeObserver();
        vto.addOnGlobalLayoutListener(new ViewTreeObserver.OnGlobalLayoutListener() {
            @Override
            public void onGlobalLayout() {
                preview.getViewTreeObserver().removeGlobalOnLayoutListener(this);
//                int width  = linearLayout.getMeasuredWidth();
//                int height = linearLayout.getMeasuredHeight();
//                c.drawBitmap(Bitmap.createBitmap(width, height, Bitmap.Config.RGB_565), 0, 0, p);

                Glide.with(mContext).asBitmap().load(url).into(new SimpleTarget<Bitmap>() {
                    @Override
                    public void onResourceReady(@NonNull Bitmap resource, @Nullable Transition<? super Bitmap> transition) {
                        int width = preview.getMeasuredWidth();
                        int diw = resource.getWidth();
                        if (diw > 0) {
                            int height = 0;
                            height = width * resource.getHeight() / diw;
                            resource = Bitmap.createScaledBitmap(resource, width, height, false);
                        }
                        preview.setImageBitmap(resource);
                    }

                });
                // Rest code skipped
            }
        });


    }



    public void setCancelable(Boolean a){
        alert.setCancelable(a);
    }

    public void show(){
        alert.show();
    }

    public void dismiss(){
        alert.dismiss();
    }
}