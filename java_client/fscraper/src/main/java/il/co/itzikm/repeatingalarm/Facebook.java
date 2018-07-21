package il.co.itzikm.repeatingalarm;

import com.facebook.AccessToken;
import com.facebook.CallbackManager;
import com.facebook.GraphRequest;
import com.facebook.GraphResponse;
import com.facebook.HttpMethod;

import org.json.JSONArray;
import org.json.JSONObject;

import java.util.HashMap;
import java.util.Map;
import java.util.concurrent.ExecutionException;

public    class Facebook  {
    public static final String TAG = "Facebook";
    AccessToken accessToken;
    CallbackManager callbackManager;

    public Facebook(AccessToken accessToken)
    {
        this.accessToken = accessToken;
        callbackManager = CallbackManager.Factory.create();
    }

    public Map<String, String> getFacebookDetails() throws InterruptedException, ExecutionException {
        final Map<String, String> detailsMap = new HashMap<>();
        GraphRequest.Callback gCallback = new GraphRequest.Callback() {
            public void onCompleted(GraphResponse response) {
                JSONObject jGraphObj = response.getJSONObject();
                try {
                    String friendId = jGraphObj.getString("id");
                    String name = jGraphObj.getString("name");
                    String email = "aa@aa.com"; //friend.getString("email");
                    detailsMap.put(friendId, name + "," + email);
                } catch (Exception e) {
                    e.printStackTrace();
                }
            }
        };
        final GraphRequest graphRequest = new GraphRequest(AccessToken.getCurrentAccessToken(), "/me", null, HttpMethod.GET, gCallback);
        // Run facebook graphRequest.
        Thread t = new Thread(new Runnable() {
            @Override
            public void run() {
                GraphResponse gResponse = graphRequest.executeAndWait();
            }
        });
        t.start();
        t.join();
        return detailsMap;
    }
}
