package com.wandertech.wandertreats.store.adapter;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.content.Context;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;

import androidx.appcompat.widget.AppCompatTextView;
import androidx.cardview.widget.CardView;
import androidx.recyclerview.widget.RecyclerView;

import com.bumptech.glide.Glide;
import com.bumptech.glide.load.engine.DiskCacheStrategy;
import com.squareup.picasso.Picasso;
import com.wandertech.wandertreats.store.MyApp;
import com.wandertech.wandertreats.store.ProductActivity;
import com.wandertech.wandertreats.store.R;
import com.wandertech.wandertreats.store.StoreActivity;
import com.wandertech.wandertreats.store.general.Data;
import com.wandertech.wandertreats.store.general.GeneralFunctions;
import com.wandertech.wandertreats.store.general.StartActProcess;
import com.wandertech.wandertreats.store.model.ItemModel;
import com.wandertech.wandertreats.store.utils.Constants;

import java.util.ArrayList;

public class MerchantItemAdapter extends RecyclerView.Adapter<MerchantItemAdapter.MyViewHolder> {
    public ArrayList<ItemModel> childModelArrayList;
    private GeneralFunctions appFunctions;
    Context mContext;
    String url = "http://mallody.com.ph/metrofresh/webimages/upload/Company/";
    public String filePath = Constants.SERVER+"uploads/store/";
    public ItemOnClickListener itemOnClickListener;

    public static class MyViewHolder extends RecyclerView.ViewHolder{
        public ImageView itemImage;
        public AppCompatTextView itemName;
        public AppCompatTextView itemDesc;
        public AppCompatTextView itemPrice;
        public CardView cardView;
        public View rightPadding, leftPadding;

        public MyViewHolder(View itemView) {
            super(itemView);

            itemImage = itemView.findViewById(R.id.item_image);
            itemName = itemView.findViewById(R.id.item_name);
            itemPrice = itemView.findViewById(R.id.item_price);
            itemDesc= itemView.findViewById(R.id.item_description);
            cardView = itemView.findViewById(R.id.cardViewArea);

            leftPadding = itemView.findViewById(R.id.leftPadding);
            rightPadding = itemView.findViewById(R.id.rightPadding);
        }
    }

    public MerchantItemAdapter(ArrayList<ItemModel> arrayList, Context mContext) {
        this.mContext = mContext;
        this.childModelArrayList = arrayList;
        this.appFunctions = MyApp.getInstance().getGeneralFun(mContext);
    }

    @Override
    public MerchantItemAdapter.MyViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {

        View view = LayoutInflater.from(parent.getContext()).inflate(R.layout.item_merchant_list, parent, false);
        return new MerchantItemAdapter.MyViewHolder(view);

    }

    @Override
    public void onBindViewHolder(MerchantItemAdapter.MyViewHolder holder, @SuppressLint("RecyclerView") int position) {
        ItemModel currentItem = childModelArrayList.get(position);

        if(position == 0){
            holder.leftPadding.setVisibility(View.VISIBLE);
        }

        if(position == getItemCount()-1){
            holder.rightPadding.setVisibility(View.VISIBLE);
        }

        holder.itemName.setText(currentItem.getTitle());
        holder.itemDesc.setText(currentItem.getDescription());

        Glide
                .with(mContext)
                .load(currentItem.getImages())
                .centerCrop()
                .skipMemoryCache(true)
                .diskCacheStrategy(DiskCacheStrategy.NONE)
                .placeholder(R.color.shimmer_placeholder)
                .into(holder.itemImage);


        // Toast.makeText(mContext, currentItem.getImages(), Toast.LENGTH_LONG).show();

        try{
            holder.cardView.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {

                    if(itemOnClickListener != null){
                        itemOnClickListener.setOnItemClick(position);
                    }

                }
            });

        }catch (Exception e){
            // Toast.makeText(mContext, e.toString(), Toast.LENGTH_LONG).show();
        }
    }

    @Override
    public int getItemCount() {
        return childModelArrayList.size();
    }

    public interface ItemOnClickListener {
        void setOnItemClick(int position);
    }

    public void setOnItemClick(ItemOnClickListener onItemClick) {
        this.itemOnClickListener = onItemClick;
    }

}