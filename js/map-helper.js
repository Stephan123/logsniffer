var bingData = "";
var cloudmadeData = "c5a3422adb935a0cac5321c8e2fcfa0c";
var bingMessage = 'You must provide your own API key for Bing Maps in the code of this sample to display geographic imagery from Bing Maps in the background content of the Geographic Map control. Replace the bingData variable with a string that contains your Bing Maps key.';

function mapHelper() {
    if (bingData === "") {
        alert(bingMessage);
    }
}
