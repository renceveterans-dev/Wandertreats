package com.wandertech.wandertreats.adapter;

import android.annotation.SuppressLint;
import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;

import com.wandertech.wandertreats.R;

import java.util.ArrayList;
import java.util.HashMap;

import androidx.appcompat.widget.AppCompatTextView;
import androidx.cardview.widget.CardView;
import androidx.recyclerview.widget.RecyclerView;

public class SavedAddressAdapter extends RecyclerView.Adapter<SavedAddressAdapter.ViewHolder> {

    Context mContext;

    ArrayList<HashMap<String, String>> recentList;
    PlacesAdapter.setRecentLocClickList locClickList;
    View view;

    public SavedAddressAdapter(Context context, ArrayList<HashMap<String, String>> list) {
        this.mContext = context;
        this.recentList = list;

    }

    @Override
    public SavedAddressAdapter.ViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {

        View view = LayoutInflater.from(mContext).inflate(R.layout.item_notification, parent, false);

        return new SavedAddressAdapter.ViewHolder(view);
    }

    @Override
    public void onBindViewHolder(SavedAddressAdapter.ViewHolder holder, @SuppressLint("RecyclerView") final int position) {

        holder.headingTxt.setText(recentList.get(position).get("main_text"));
        holder.messageTxt.setText(recentList.get(position).get("secondary_text"));
        holder.dateTimeTxt.setText(recentList.get(position).get("secondary_text"));

//        holder.placeAdapterView.setOnClickListener(new View.OnClickListener() {
//            @Override
//            public void onClick(View view) {
//                if (locClickList != null) {
//                    locClickList.itemRecentLocClick(position);
//                }
//            }
//        });

    }

    @Override
    public int getItemCount() {
        return recentList.size();
    }

    public void itemRecentLocClick(PlacesAdapter.setRecentLocClickList setRecentLocClickList) {
        this.locClickList = setRecentLocClickList;
    }

    public interface setRecentLocClickList {
        void itemRecentLocClick(int position);
    }

    public class ViewHolder extends RecyclerView.ViewHolder {

        AppCompatTextView dateTimeTxt;
        AppCompatTextView messageTxt;
        AppCompatTextView headingTxt;
        CardView notifCard;

        public ViewHolder(View itemView) {
            super(itemView);

            messageTxt = (AppCompatTextView) itemView.findViewById(R.id.messageTxt);
            dateTimeTxt = (AppCompatTextView) itemView.findViewById(R.id.dateTimeTxt );
            headingTxt = (AppCompatTextView) itemView.findViewById(R.id.headingTxt );
            notifCard = (CardView) itemView.findViewById(R.id.notifCard);

        }
    }

}
