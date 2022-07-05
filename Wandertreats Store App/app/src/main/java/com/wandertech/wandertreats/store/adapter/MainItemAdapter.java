package com.wandertech.wandertreats.store.adapter;

import android.app.Activity;
import android.content.Context;
import android.graphics.Color;
import android.os.Bundle;
import android.text.SpannableString;
import android.text.SpannableStringBuilder;
import android.text.Spanned;
import android.text.style.ForegroundColorSpan;
import android.text.style.StrikethroughSpan;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;

import androidx.appcompat.widget.AppCompatTextView;
import androidx.cardview.widget.CardView;
import androidx.recyclerview.widget.RecyclerView;

import com.bumptech.glide.Glide;
import com.bumptech.glide.load.engine.DiskCacheStrategy;
import com.squareup.picasso.Picasso;
import com.wandertech.wandertreats.store.MyApp;
import com.wandertech.wandertreats.store.ProductActivity;
import com.wandertech.wandertreats.store.R;
import com.wandertech.wandertreats.store.general.Data;
import com.wandertech.wandertreats.store.general.GeneralFunctions;
import com.wandertech.wandertreats.store.general.StartActProcess;
import com.wandertech.wandertreats.store.model.ItemModel;
import com.wandertech.wandertreats.store.utils.Constants;

import java.util.ArrayList;

public class MainItemAdapter extends RecyclerView.Adapter<MainItemAdapter.MyViewHolder> {
    public ArrayList<ItemModel> childModelArrayList;
    private GeneralFunctions appFunctions;
    Context mContext;
    String url = "http://mallody.com.ph/metrofresh/webimages/upload/Company/";
    public String filePath = Constants.SERVER+"uploads/products/";
    public String storefilePath = Constants.SERVER+"uploads/store/";

    public static class MyViewHolder extends RecyclerView.ViewHolder{
        public ImageView itemImage, itemStoreImage;
        public AppCompatTextView itemName;
        public AppCompatTextView itemDesc;
        public AppCompatTextView itemPrice;
        public AppCompatTextView discountTxt;
        public AppCompatTextView itemBaseprice;
        public CardView cardView;
        public View rightPadding, leftPadding;

        public MyViewHolder(View itemView) {
            super(itemView);

            itemImage = itemView.findViewById(R.id.item_image);
            itemName = itemView.findViewById(R.id.item_name);
            itemPrice = itemView.findViewById(R.id.item_price);
            itemStoreImage = itemView.findViewById(R.id.item_store_image);
            itemBaseprice = itemView.findViewById(R.id.item_baseprice);
            itemDesc= itemView.findViewById(R.id.item_description);
            cardView = itemView.findViewById(R.id.cardViewArea);
            discountTxt = itemView.findViewById(R.id.discountTxt);
            leftPadding = itemView.findViewById(R.id.leftPadding);
            rightPadding = itemView.findViewById(R.id.rightPadding);
        }
    }

    public MainItemAdapter(ArrayList<ItemModel> arrayList, Context mContext) {
        this.mContext = mContext;
        this.childModelArrayList = arrayList;
        this.appFunctions = MyApp.getInstance().getGeneralFun(mContext);
    }

    @Override
    public MyViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {

        View view = LayoutInflater.from(parent.getContext()).inflate(R.layout.item_main_list, parent, false);
        return new MyViewHolder(view);

    }

    @Override
    public void onBindViewHolder(MyViewHolder holder, int position) {
        ItemModel currentItem = childModelArrayList.get(position);

        if(position == 0){
            holder.leftPadding.setVisibility(View.VISIBLE);
        }

        if(position == getItemCount()-1){
            holder.rightPadding.setVisibility(View.VISIBLE);
        }

        try{

            holder.itemBaseprice.setText(appFunctions.getDecimalWithSymbol(appFunctions.getJsonValue("fBasePrice", currentItem.getData())));
            holder.itemPrice.setText(appFunctions.getDecimalWithSymbol(appFunctions.getJsonValue("fPrice", currentItem.getData())));

            holder.itemName.setText(currentItem.getTitle());
            holder.itemDesc.setText("#"+appFunctions.getJsonValue("iTotalSold", currentItem.getData())+" Sold" );
            holder.discountTxt.setText(appFunctions.getJsonValue("fDiscount", currentItem.getData())+"% OFF");

            Glide
                    .with(mContext)
                    .load( currentItem.getThumbnail())
                    .centerCrop()
                    .skipMemoryCache(true)
                    .diskCacheStrategy(DiskCacheStrategy.NONE)
                    .placeholder(R.color.shimmer_placeholder)
                    .into(holder.itemImage);

            Glide
                    .with(mContext)
                    .load( currentItem.getThumbnail())
                    .centerCrop()
                    .skipMemoryCache(true)
                    .diskCacheStrategy(DiskCacheStrategy.NONE)
                    .placeholder(R.color.shimmer_placeholder)
                    .into(holder.itemStoreImage);


        }catch (Exception e){
            Toast.makeText(mContext, e.toString(), Toast.LENGTH_LONG).show();
        }


        try{
            holder.cardView.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {
                    Bundle bn =  new Bundle();
                    bn.putString("data", currentItem.getData());
                    new StartActProcess(mContext).startActWithData(ProductActivity.class,bn);
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


}