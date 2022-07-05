package com.wandertech.wandertreats.store.adapter;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.webkit.URLUtil;
import android.widget.ImageView;

import com.bumptech.glide.Glide;
import com.bumptech.glide.load.engine.DiskCacheStrategy;
import com.squareup.picasso.Picasso;
import com.wandertech.wandertreats.store.R;
import com.wandertech.wandertreats.store.utils.Constants;

import java.util.ArrayList;
import java.util.HashMap;

import androidx.appcompat.widget.AppCompatTextView;
import androidx.cardview.widget.CardView;
import androidx.recyclerview.widget.RecyclerView;

public class FeedPostAdapter extends RecyclerView.Adapter<FeedPostAdapter.ViewHolder> {

    Context mContext;

    ArrayList<HashMap<String, String>> feedList;
    setOnClickList ItemClickList;
    View view;
    public String newsfeedsfilePath = Constants.SERVER+"uploads/newsfeeds/";

    public FeedPostAdapter(Context context, ArrayList<HashMap<String, String>> list) {
        this.mContext = context;
        this.feedList = list;

    }

    @Override
    public ViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {

        View view = LayoutInflater.from(mContext).inflate(R.layout.item_feeds, parent, false);

        return new ViewHolder(view);
    }

    @Override
    public void onBindViewHolder(ViewHolder holder, @SuppressLint("RecyclerView") final int position) {

        boolean isValid = URLUtil.isValidUrl(feedList.get(position).get("vImage"));
        if(isValid){

            // Load Images
            ((Activity) mContext).runOnUiThread(new Runnable() {
                public void run() {
                    Glide
                        .with(mContext)
                        .load( feedList.get(position).get("vImage"))
                        .centerCrop()
                        .skipMemoryCache(true)
                        .diskCacheStrategy(DiskCacheStrategy.NONE)
                        .placeholder(mContext.getResources().getDrawable(R.color.gray))
                        .into(holder.itemThumbnail);

                }
            });



        }else{

            Glide
                    .with(mContext)
                    .load( feedList.get(position).get("vImage"))
                    .centerCrop()
                    .skipMemoryCache(true)
                    .diskCacheStrategy(DiskCacheStrategy.NONE)
                    .placeholder(mContext.getResources().getDrawable(R.color.gray))
                    .into(holder.itemThumbnail);

        }


        holder.messageTxt.setText(feedList.get(position).get("vMessage"));
        holder.dateTxt.setText(feedList.get(position).get("dDate"));
        holder.titleTxt.setText(feedList.get(position).get("vTitle"));
        holder.itemCard.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                if (ItemClickList != null) {
                    ItemClickList.itemOnClick(position);
                }
            }
        });

    }

    @Override
    public int getItemCount() {
        return feedList.size();
    }

    public void itemOnClick(setOnClickList setOnClickList) {
        this.ItemClickList = setOnClickList;
    }

    public interface setOnClickList {
        void itemOnClick(int position);
    }

    public class ViewHolder extends RecyclerView.ViewHolder {

        AppCompatTextView titleTxt, messageTxt, dateTxt;
        ImageView itemThumbnail;
        CardView itemCard;

        public ViewHolder(View itemView) {
            super(itemView);

            dateTxt = (AppCompatTextView) itemView.findViewById(R.id.dateTxt) ;
            messageTxt = (AppCompatTextView) itemView.findViewById(R.id.messageTxt);
            titleTxt = (AppCompatTextView) itemView.findViewById(R.id.titleTxt);
            itemThumbnail = (ImageView) itemView.findViewById(R.id.itemThumbnail);
            itemCard = (CardView) itemView.findViewById(R.id.itemCard);

        }
    }

}
