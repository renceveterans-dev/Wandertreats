package com.wandertech.wandertreats.general;


import android.app.AlertDialog;
import android.content.Context;
import android.graphics.drawable.ColorDrawable;
import android.view.LayoutInflater;
import android.view.View;

import com.google.android.material.button.MaterialButton;
import com.wandertech.wandertreats.R;

import androidx.appcompat.widget.AppCompatTextView;

public class PopUpDialog  {

    private Context mContext;
    private AlertDialog.Builder builder;
    private LayoutInflater inflater;
    private View dialog;
    private AppCompatTextView title;
    private AppCompatTextView content;
    private MaterialButton positive_btn;
    private View seperator4;
    private AlertDialog alert;

    public PopUpDialog(Context contaxt, String a){
        this.mContext = contaxt;
        this.builder = new AlertDialog.Builder(contaxt);
        this.inflater = (LayoutInflater) mContext.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
        this.dialog = inflater.inflate(R.layout.dialog_alert, null);

        title = dialog.findViewById(R.id.title);
        content = dialog.findViewById(R.id.message);
        positive_btn = dialog.findViewById(R.id.positive_btn);
        seperator4 = dialog.findViewById(R.id.seperator4);
        builder.setView(dialog);

        title.setText(a);

        alert = builder.create();
        alert.getWindow().setBackgroundDrawable(new ColorDrawable(android.graphics.Color.TRANSPARENT));
        alert.show();


    }

    public void setButtonVisible(Boolean value){

        if(value){


            positive_btn.setVisibility(View.VISIBLE);
        }else{
            positive_btn.setVisibility(View.GONE);
        }
    }

    public void setTile(String a){
        title.setText(a);
    }
    public void setTilteVisible(Boolean a){
        if(a){
            title.setVisibility(View.VISIBLE);
            seperator4.setVisibility(View.VISIBLE);
        }else{
            title.setVisibility(View.GONE);
            seperator4.setVisibility(View.GONE);
        }



    }

    public void setMessage(String a){
        content.setText(a);
    }

    public void setPositiveBtn(String a){
        positive_btn.setText(a);
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

    public MaterialButton getPositive_btn(){
        return  positive_btn;
    }

}
