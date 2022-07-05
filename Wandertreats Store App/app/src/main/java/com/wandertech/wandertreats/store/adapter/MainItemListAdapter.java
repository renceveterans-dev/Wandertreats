package com.wandertech.wandertreats.store.adapter;

import android.app.Activity;
import android.content.Context;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.Toast;

import com.bumptech.glide.Glide;
import com.bumptech.glide.load.engine.DiskCacheStrategy;
import com.squareup.picasso.Picasso;
import com.wandertech.wandertreats.store.MyApp;
import com.wandertech.wandertreats.store.ProductActivity;
import com.wandertech.wandertreats.store.R;
import com.wandertech.wandertreats.store.general.GeneralFunctions;
import com.wandertech.wandertreats.store.general.StartActProcess;
import com.wandertech.wandertreats.store.model.ItemModel;
import com.wandertech.wandertreats.store.utils.Constants;

import java.util.ArrayList;

import androidx.appcompat.widget.AppCompatTextView;
import androidx.appcompat.widget.LinearLayoutCompat;
import androidx.cardview.widget.CardView;
import androidx.recyclerview.widget.RecyclerView;

public class MainItemListAdapter extends RecyclerView.Adapter<MainItemListAdapter.MyViewHolder> {
    public ArrayList<ItemModel> childModelArrayList;
    private GeneralFunctions appFunctions;
    Context mContext;
    String url = "http://mallody.com.ph/metrofresh/webimages/upload/Company/";
    public String filePath = Constants.SERVER+"uploads/products/";

    public static class MyViewHolder extends RecyclerView.ViewHolder{
        public ImageView itemImage;
        public AppCompatTextView itemName;
        public AppCompatTextView itemDesc;
        public AppCompatTextView itemPrice;
        public AppCompatTextView discountTxt;
        public AppCompatTextView itemBaseprice;
        public CardView cardView;
        public View rightPadding, leftPadding;
        public LinearLayoutCompat viewArea;

        public MyViewHolder(View itemView) {
            super(itemView);

            itemImage = itemView.findViewById(R.id.item_image);
            itemName = itemView.findViewById(R.id.item_name);
            itemPrice = itemView.findViewById(R.id.item_price);
            itemBaseprice = itemView.findViewById(R.id.item_baseprice);
            itemDesc= itemView.findViewById(R.id.item_description);
            cardView = itemView.findViewById(R.id.cardViewArea);
            discountTxt = itemView.findViewById(R.id.discountTxt);
            leftPadding = itemView.findViewById(R.id.leftPadding);
            rightPadding = itemView.findViewById(R.id.rightPadding);
            viewArea = itemView.findViewById(R.id.viewArea);
        }
    }

    public MainItemListAdapter(ArrayList<ItemModel> arrayList, Context mContext) {
        this.mContext = mContext;
        this.childModelArrayList = arrayList;
        this.appFunctions = MyApp.getInstance().getGeneralFun(mContext);
    }

    @Override
    public MainItemListAdapter.MyViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {

        View view = LayoutInflater.from(parent.getContext()).inflate(R.layout.item_produtcs, parent, false);
        return new MainItemListAdapter.MyViewHolder(view);

    }

    @Override
    public void onBindViewHolder(MainItemListAdapter.MyViewHolder holder, int position) {
        ItemModel currentItem = childModelArrayList.get(position);

        try{

            holder.itemBaseprice.setText(appFunctions.getDecimalWithSymbol(appFunctions.getJsonValue("fBasePrice", currentItem.getData())));
            holder.itemPrice.setText(appFunctions.getDecimalWithSymbol(appFunctions.getJsonValue("fPrice", currentItem.getData())));

            holder.itemName.setText(currentItem.getTitle());
            holder.itemDesc.setText("#"+appFunctions.getJsonValue("iTotalSold", currentItem.getData())+" Sold" );
            holder.discountTxt.setText(appFunctions.getJsonValue("fDiscount", currentItem.getData())+"% OFF");

//            ((Activity) mContext).runOnUiThread(new Runnable() {
//                public void run() {
//
//
//                }
//            })
//
//            ;

            Glide
                    .with(mContext)
                    .load( currentItem.getThumbnail())
                    .centerCrop()
                    .skipMemoryCache(true)
                    .diskCacheStrategy(DiskCacheStrategy.NONE)
                    .placeholder(R.color.shimmer_placeholder)
                    .into(holder.itemImage);

        }catch (Exception e){
            Toast.makeText(mContext, e.toString(), Toast.LENGTH_LONG).show();
        }


        try{

            if(!appFunctions.getJsonValue("iStocks", currentItem.getData()).equals("0")){
                holder.viewArea.setAlpha(1);
                holder.viewArea.setOnClickListener(new View.OnClickListener() {
                    @Override
                    public void onClick(View view) {
                        Bundle bn =  new Bundle();
                        bn.putString("data", currentItem.getData());
                        new StartActProcess(mContext).startActWithData(ProductActivity.class,bn);
                    }
                });
            }else{
                holder.viewArea.setAlpha(0.5f);
            }


        }catch (Exception e){
            // Toast.makeText(mContext, e.toString(), Toast.LENGTH_LONG).show();
        }
    }

    @Override
    public int getItemCount() {
        return childModelArrayList.size();
    }


}