package com.wandertech.wandertreats.adapter;

import android.annotation.SuppressLint;
import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;

import androidx.appcompat.widget.AppCompatTextView;
import androidx.cardview.widget.CardView;
import androidx.core.content.ContextCompat;
import androidx.recyclerview.widget.RecyclerView;

import com.wandertech.wandertreats.MyApp;
import com.wandertech.wandertreats.R;
import com.wandertech.wandertreats.general.GeneralFunctions;

import java.util.ArrayList;
import java.util.HashMap;

import static com.firebase.ui.auth.AuthUI.getApplicationContext;

public class MyTreatsAdapter extends RecyclerView.Adapter<MyTreatsAdapter.ViewHolder> {

    Context mContext;
    private ItemOnClickListener itemOnClickListener;
    ArrayList<HashMap<String, String>> recentList;
    PlacesAdapter.setRecentLocClickList locClickList;
    private GeneralFunctions appFunctions;
    View view;

    public MyTreatsAdapter(Context context, ArrayList<HashMap<String, String>> list) {
        this.mContext = context;
        this.recentList = list;
        this.appFunctions = MyApp.getInstance().getGeneralFun(context);

    }

    @Override
    public MyTreatsAdapter.ViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {

        View view = LayoutInflater.from(mContext).inflate(R.layout.item_purchased, parent, false);
        return new MyTreatsAdapter.ViewHolder(view);
    }

    @Override
    public void onBindViewHolder(MyTreatsAdapter.ViewHolder holder, @SuppressLint("RecyclerView") final int position) {

        holder.titleTxt.setText(recentList.get(position).get("vPurchaseName"));
        holder.messageTxt.setText("Ref:"+recentList.get(position).get("vPurchaseNo"));
        holder.infoTxt.setText(recentList.get(position).get("vName")+" "+recentList.get(position).get("vLastName"));


        String data = recentList.get(position).get("data");

        if(appFunctions.getJsonValue("iStatusCode", data).equalsIgnoreCase("1")){
            holder.warningIcon.setVisibility(View.VISIBLE);
        }

        if(appFunctions.getJsonValue("iStatusCode", data).equalsIgnoreCase("5")){
            holder.warningIcon.setVisibility(View.VISIBLE);
            holder.warningIcon.setBackgroundTintList(ContextCompat.getColorStateList(mContext, R.color.appThemeColor_success));
        }

        if(appFunctions.getJsonValue("iStatusCode", data).equalsIgnoreCase("7")){
            holder.warningIcon.setVisibility(View.GONE);
            holder.messageTxt.setText("Ref:"+recentList.get(position).get("vPurchaseNo")+" | Item Expired");
        }

        holder.itemCard.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                if (itemOnClickListener != null) {
                    itemOnClickListener.setOnItemClick(position);
                }
            }
        });

    }

    @Override
    public int getItemCount() {
        return recentList.size();
    }

    public class ViewHolder extends RecyclerView.ViewHolder {

        AppCompatTextView infoTxt;
        AppCompatTextView messageTxt;
        AppCompatTextView titleTxt;
        ImageView warningIcon;
        CardView itemCard;

        public ViewHolder(View itemView) {
            super(itemView);

            messageTxt = (AppCompatTextView) itemView.findViewById(R.id.messageTxt);
            infoTxt = (AppCompatTextView) itemView.findViewById(R.id.infoTxt );
            titleTxt = (AppCompatTextView) itemView.findViewById(R.id.titleTxt);
            itemCard = (CardView) itemView.findViewById(R.id.itemCard);
            warningIcon = (ImageView) itemView.findViewById(R.id.warningIcon);
        }
    }

    public interface ItemOnClickListener {
        void setOnItemClick(int position);
    }

    public void setOnItemClick(ItemOnClickListener onItemClick) {
        this.itemOnClickListener = onItemClick;
    }

}
