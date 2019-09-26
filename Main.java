import java.util.Scanner;
import java.util.regex.Matcher;
import java.util.regex.Pattern;


public class Main {
    public static void main(String[] args) {
        Scanner scanner = new Scanner(System.in);
        String input = "";
        System.out.println("HOW ARE YOU?");

        Pattern patternAll = Pattern.compile("\\ball\\b");
        Pattern patternAlways = Pattern.compile("\\balways\\b");
        Pattern patternMy = Pattern.compile("\\bmy\\b");
        Pattern patternMe = Pattern.compile("\\bme\\b");
        Pattern patternNegative = Pattern.compile("(\\bdepressed\\b|\\bsad\\b|\\bupset\\b)");


        do {
            input = scanner.nextLine();
            Matcher mAll = patternAll.matcher(input);
            Matcher mAlways = patternAlways.matcher(input);
            Matcher mMy = patternMy.matcher(input);
            Matcher mMe = patternMe.matcher(input);
            Matcher mNegative = patternNegative.matcher(input);
            if (mAll.find()) {
                System.out.println("IN WHAT WAY?");
                continue;
            }

            if (mAlways.find()) {
                System.out.println("CAN YOU THINK OF A SPECIFIC EXAMPLE?");
                continue;
            }

            if (mMy.find()) {
                String output = "your" + input.substring(mMy.end());
                Matcher submat = patternMe.matcher(output);
                if (submat.find()) {
                    String reply = output.substring(0, submat.start() - 1) + " you" + output.substring(submat.end());
                    System.out.println(reply.toUpperCase());
                } else {
                    System.out.println(output.toUpperCase());
                }
                continue;
            }

            if (mNegative.find()) {
                String reply = "I AM SORRY TO HEAR YOU ARE " + input.substring(mNegative.start(), mNegative.end());
                System.out.println(reply.toUpperCase());
            }

        }
        while (!input.equals("end"));
    }
}
