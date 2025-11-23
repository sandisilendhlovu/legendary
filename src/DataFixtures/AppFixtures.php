<?php

namespace App\DataFixtures;

use App\Entity\Flow;
use App\Entity\FlowStep;
use App\Entity\FlowStepOption;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        //
        // CREATE FLOW — Fibre: Slow Internet Access
        //

        $slowFlow = new Flow();
        $slowFlow->setTitle('Fibre — Slow Internet Access');
        $slowFlow->setDescription('A guided troubleshooting flow for slow speeds on fibre connections.');
        $manager->persist($slowFlow);


        //
        // STEP 1 — Check for outages
        //

        $step1 = new FlowStep();
        $step1->setFlow($slowFlow);
        $step1->setStepNumber(1);
        $step1->setContent(
            "Check if the client is not affected by any outages.\n\n" .
            "• Check the Afrihost Network Status page or the FNO portal (where applicable) to confirm no open or ongoing outages exist in the client's area.\n"
        );
        $manager->persist($step1);


        //
        // STEP 2 — Verify the correct speed profile (Mojo + FNO)
        //

        $step2 = new FlowStep();
        $step2->setFlow($slowFlow);
        $step2->setStepNumber(2);
        $step2->setContent(
            "Verify the client's package and speed profile.\n\n" .
            "• Confirm the subscribed speed in Mojo.\n" .
            "• Check the FNO portal for the correct speed profile.\n" .
            "• Ensure there are no mismatches between the client's line speed and their provisioned profile."
        );
        $manager->persist($step2);


        //
        // STEP 3 — Check NIC speed negotiation + LAN cable
        //

        $step3 = new FlowStep();
        $step3->setFlow($slowFlow);
        $step3->setStepNumber(3);
        $step3->setContent(
            "Verify the client's computer and LAN cable can support the subscribed speed.\n\n" .
            "• If a client is subscribed to a 100Mbps line or higher, confirm the PC's Ethernet NIC is 1Gbps capable.\n" .
            "• Ensure the client is using a CAT5e or CAT6 LAN cable.\n"
        );
        $manager->persist($step3);


        //
        // STEP 4 — Bypass router, test directly on ONT
        //

        $step4 = new FlowStep();
        $step4->setFlow($slowFlow);
        $step4->setStepNumber(4);
        $step4->setContent(
            "Test the client's speeds while connected directly to the ONT.\n\n" .
            "• Have the client bypass the router & connect their PC directly to the ONT via LAN.\n" .
            "• Run a speed test.\n" .
            "• Compare the obtained speeds with the subscribed speeds.\n\n" .
            "If the speeds are correct directly on the ONT, this indicates that all is in order with the configured speeds on the fibre line — proceed to router troubleshooting."
        );
        $manager->persist($step4);


        //
        // STEP 5 — Test router via LAN, disable Wi-Fi, check bandwidth usage
        //

        $step5 = new FlowStep();
        $step5->setFlow($slowFlow);
        $step5->setStepNumber(5);
        $step5->setContent(
            "Reconnect the router and perform a controlled LAN test.\n\n" .
            "• Disable the Wi-Fi network.\n" .
            "• Disconnect all other devices.\n" .
            "• Connect a single PC to the router via LAN.\n" .
            "• Ensure the client has no background downloads or apps using bandwidth.\n" .
            "• Run a speed test again."
        );
        $manager->persist($step5);


        //
        // STEP 6 — Improve Wi-Fi speeds (manual channel selection)
        //

        $step6 = new FlowStep();
        $step6->setFlow($slowFlow);
        $step6->setStepNumber(6);
        $step6->setContent(
            "Improve wireless performance by adjusting Wi-Fi channel settings.\n\n" .
            "• Log into the router interface.\n" .
            "• Manually select a wireless channel.\n\n" .
            "Recommended non-overlapping channels:\n" .
            "• 2.4 GHz: Channels 1, 6, or 11\n" .
            "• 5 GHz: Channels 36, 40, 44, or 48\n\n" .
            "• Test connection stability and speed after changing channels."
        );
        $manager->persist($step6);


        //
        // STEP 7 — If slow at ONT: Gather evidence + log FNO fault
        //

        $step7 = new FlowStep();
        $step7->setFlow($slowFlow);
        $step7->setStepNumber(7);
        $step7->setContent(
            "If speeds are slow even when connected directly to the ONT, this indicates a provider/FNO/line issue.\n\n" .
            "Collect the following before logging a fault:\n" .
            "• Speed test screenshot (full window, uncropped).\n" .
            "• Traceroute to a local server.\n" .
            "• Traceroute to an international server.\n\n" .
            "Note: Some FNOs have special requirements (e.g., Frogfoot requires the speed test + Task Manager screenshot showing CPU usage).\n\n" .
            "Once collected, log an informative and detailed fault with the provider."
        );
        $manager->persist($step7);


        //
        // Save everything
        //

        $manager->flush();
    }
}




