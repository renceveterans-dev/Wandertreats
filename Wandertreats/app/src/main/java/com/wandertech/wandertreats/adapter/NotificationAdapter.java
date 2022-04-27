package com.wandertech.wandertreats.adapter;

import android.annotation.SuppressLint;
import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.LinearLayout;

import androidx.appcompat.widget.AppCompatTextView;
import androidx.appcompat.widget.LinearLayoutCompat;
import androidx.cardview.widget.CardView;
import androidx.recyclerview.widget.RecyclerView;

import com.wandertech.wandertreats.MyApp;
import com.wandertech.wandertreats.R;
import com.wandertech.wandertreats.general.GeneralFunctions;

import java.util.ArrayList;
import java.util.HashMap;

public class NotificationAdapter extends RecyclerView.Adapter<NotificationAdapter.ViewHolder> {

    Context mContext;

    ArrayList<HashMap<String, String>> notifList;
    ItemClickList itemClickList;
    GeneralFunctions appFunctions;
    View view;

    public NotificationAdapter(Context context, ArrayList<HashMap<String, String>> list) {
        this.mContext = context;
        this.notifList = list;
        this.appFunctions = MyApp.getInstance().getGeneralFun(context);

    }

    @Override
    public ViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {

        View view = LayoutInflater.from(mContext).inflate(R.layout.item_notification, parent, false);

        return new ViewHolder(view);
    }

    @Override
    public void onBindViewHolder(ViewHolder holder, @SuppressLint("RecyclerView") final int position) {

        holder.headingTxt.setText(notifList.get(position).get("vTitle"));
        holder.messageTxt.setText(notifList.get(position).get("vDescription"));
        holder.dateTimeTxt.setText(notifList.get(position).get("dDateCreated"));

        holder.notifView.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                if(itemClickList != null){
                    itemClickList.onItemClick(position);
                }
                //appFunctions.showMessage("hajhsashas");
            }
        });

        if(notifList.get(position).get("eStatus").equalsIgnoreCase("Unread")){
            holder.warningIcon.setVisibility(View.VISIBLE);
            holder.notifView.setBackgroundColor(mContext.getResources().getColor(R.color.appThemeColor_fade_light));
        }else{
            holder.warningIcon.setVisibility(View.GONE);
            holder.notifView.setBackgroundColor(mContext.getResources().getColor(R.color.white));
        }


    }

    @Override
    public int getItemCount() {
        return notifList.size();
    }

    public void onItemClick(ItemClickList setItemClickList) {
        this.itemClickList = setItemClickList;
    }

    public interface ItemClickList {
        void onItemClick(int position);
    }

    public class ViewHolder extends RecyclerView.ViewHolder {

        AppCompatTextView dateTimeTxt;
        AppCompatTextView messageTxt;
        AppCompatTextView headingTxt;
        CardView notifCard;
        LinearLayoutCompat notifView;
        ImageView warningIcon;

        public ViewHolder(View itemView) {
            super(itemView);

            notifView = (LinearLayoutCompat) itemView.findViewById(R.id.notifView);
            messageTxt = (AppCompatTextView) itemView.findViewById(R.id.messageTxt);
            dateTimeTxt = (AppCompatTextView) itemView.findViewById(R.id.dateTimeTxt );
            headingTxt = (AppCompatTextView) itemView.findViewById(R.id.headingTxt );
            notifCard = (CardView) itemView.findViewById(R.id.notifCard);
            warningIcon = (ImageView) itemView.findViewById(R.id.warningIcon);

        }
    }

}
