package com.hardness.web;

import android.app.Activity;
import android.app.AlertDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.net.Uri;
import android.os.Bundle;
import android.view.KeyEvent;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.widget.Toast;

public class HardnessWeb extends Activity {
	WebView myWebView;
	HardnessWeb myActivity = this;
	String goUrl;
	String urlBase = "http://201.22.57.137:8282/hardness3/mobile/";
    /** Called when the activity is first created. */
    @Override
    public void onCreate(Bundle icicle) {
    	super.onCreate(icicle);
        setContentView(R.layout.main);

        // Set the webview
        myWebView = (WebView) findViewById(R.id.webkit);
        
        // Bind javascript
        myWebView.addJavascriptInterface(new JavaScriptInterface(this), "Hardness");
        
        // Enable JS
        myWebView.getSettings().setJavaScriptEnabled(true);
        
        // Open link in the webview
        myWebView.setWebViewClient(new MyWebViewClient());
        
        // overview mode
        //myWebView.getSettings().setUseWideViewPort(true);
        //myWebView.getSettings().setLoadWithOverviewMode(true);
        
        // Open it!
        myWebView.loadUrl(urlBase);
    }
    
    // Device Buttons
    @Override
    public boolean onKeyDown(int keyCode, KeyEvent event) {
        // Check if the key event was the Back button and if there's history
        if ((keyCode == KeyEvent.KEYCODE_BACK) && myWebView.canGoBack()) {
            myWebView.goBack();
            return true;
        }        
        // If it wasn't the Back key or there's no web page history, bubble up to the default
        // system behavior (probably exit the activity)
        return super.onKeyDown(keyCode, event);
    }
    
    // Override the default click method to links
    private class MyWebViewClient extends WebViewClient  {
        @Override
        public boolean shouldOverrideUrlLoading(WebView view, String url) {
            if (Uri.parse(url).getHost().equals("201.22.57.137")) {
                // This is my web site, so do not override; let my WebView load the page
                return false;
            }
            // Otherwise, the link is not for a page on my site, so launch another Activity that handles URLs
            Intent intent = new Intent(Intent.ACTION_VIEW, Uri.parse(url));
            startActivity(intent);
            return true;
        }
    }
    
    // JS
    public class JavaScriptInterface {
        Context mContext;

        /** Instantiate the interface and set the context */
        JavaScriptInterface(Context c) {
            mContext = c;
        }

        /** Show a toast from the web page */
        public void showToast(String toast) {
            Toast.makeText(mContext, toast, Toast.LENGTH_LONG).show();
        }
        
        // Exit button
        public void exit() {
        	finish();
        	System.exit(0);
        }
        
        // Browser Back
        public void browserBack() {
        	myWebView.goBack();
        }
        
        // Browser Clear History
        public void browserClearHistory() {
        	myWebView.clearHistory();
        }
        
        // Yes/No Dialog
        public void browserDialogYesNo(String url) {
        	goUrl = url;
        	AlertDialog.Builder builder = new AlertDialog.Builder(myActivity);
            builder.setMessage("Confirmar?")
                   .setCancelable(false)
                   .setPositiveButton("Yes", new DialogInterface.OnClickListener() {
                       public void onClick(DialogInterface dialog, int id) {
                            //MyActivity.this.finish();
                    	   myWebView.loadUrl(urlBase + goUrl);
                       }
                   })
                   .setNegativeButton("No", new DialogInterface.OnClickListener() {
                       public void onClick(DialogInterface dialog, int id) {
                            dialog.cancel();
                       }
                   });
            AlertDialog alert = builder.create();
            // Title for AlertDialog
            //alert.setTitle("AlertDialogExample");
            // Icon for AlertDialog
            //alert.setIcon(R.drawable.ic_launcher);
            alert.show();
        }
    }

}