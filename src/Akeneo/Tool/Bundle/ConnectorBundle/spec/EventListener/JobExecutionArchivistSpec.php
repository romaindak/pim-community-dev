<?php

namespace spec\Akeneo\Tool\Bundle\ConnectorBundle\EventListener;

use Akeneo\Tool\Bundle\ConnectorBundle\EventListener\JobExecutionArchivist;
use Akeneo\Tool\Component\Batch\Event\EventInterface;
use Akeneo\Tool\Component\Batch\Event\JobExecutionEvent;
use Akeneo\Tool\Component\Batch\Model\JobExecution;
use Akeneo\Tool\Component\Connector\Archiver\ArchiverInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class JobExecutionArchivistSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(JobExecutionArchivist::class);
    }

    function it_is_an_event_subscriber()
    {
        $this->shouldHaveType('Symfony\Component\EventDispatcher\EventSubscriberInterface');
    }

    function it_returns_subscribed_events()
    {
        $this->getSubscribedEvents()->shouldReturn(
            [
                EventInterface::BEFORE_JOB_STATUS_UPGRADE => 'beforeStatusUpgrade'
            ]
        );
    }

    function it_throws_an_exception_if_there_is_already_a_registered_archiver(ArchiverInterface $archiver)
    {
        $archiver->getName()->willReturn('output');

        $this->registerArchiver($archiver);
        $this->shouldThrow('\InvalidArgumentException')->during('registerArchiver', [$archiver]);
    }

    function it_returns_generated_archives(
        JobExecution $jobExecution,
        ArchiverInterface $archiver,
        ArchiverInterface $archiver2,
        ArchiverInterface $archiver3
    ) {
        $jobExecution->isRunning()->willReturn(false);

        $archiver->getName()->willReturn('output');
        $archiver->getArchives($jobExecution, false)->willYield(
            ['log.log' => 'a/b/log.log', 'test.png' => 'a/b/test.png']
        );
        $this->registerArchiver($archiver);

        $archiver2->getName()->willReturn('input');
        $archiver2->getArchives($jobExecution, false)->willYield(
            ['image.jpg' => 'a/c/d/image.jpg', 'notice.pdf' => 'b/c/d/notice.pdf']
        );
        $this->registerArchiver($archiver2);

        $archiver3->getName()->willReturn('invalid_items');
        $archiver3->getArchives($jobExecution, false)->willYield([]);
        $this->registerArchiver($archiver3);

        $this->getArchives($jobExecution)->shouldReturn([
            'output' => ['log.log' => 'a/b/log.log', 'test.png' => 'a/b/test.png'],
            'input' => ['image.jpg' => 'a/c/d/image.jpg', 'notice.pdf' => 'b/c/d/notice.pdf']
        ]);
    }

    function it_does_not_return_archives_if_the_job_is_still_running(
        JobExecution $jobExecution,
        ArchiverInterface $archiver
    ) {
        $archiver->getName()->willReturn('output');
        $this->registerArchiver($archiver);

        $jobExecution->isRunning()->willReturn(true);
        $archiver->getArchives($jobExecution, Argument::cetera())->shouldNotBeCalled();
        $this->getArchives($jobExecution)->shouldReturn([]);
    }

    function it_throws_an_exception_if_no_archiver_is_defined(
        JobExecution $jobExecution,
        ArchiverInterface $archiver
    ) {
        $archiver->getName()->willReturn('archiver');

        $this->registerArchiver($archiver);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('getArchive', [$jobExecution, 'archiver_name', 'key']);
    }

    function it_returns_the_corresponding_archiver(JobExecution $jobExecution, ArchiverInterface $archiver)
    {
        $archiver->getName()->willReturn('output');
        $archiver->getArchive($jobExecution, 'key')->shouldBeCalled();
        $this->registerArchiver($archiver);

        $this->getArchive($jobExecution, 'output', 'key');
    }

    function it_register_an_event_and_verify_if_job_is_supported(
        JobExecutionEvent $event,
        JobExecution $jobExecution,
        ArchiverInterface $archiver1,
        ArchiverInterface $archiver2
    ) {
        $archiver1->getName()->willReturn('archiver_1');
        $archiver2->getName()->willReturn('archiver_2');

        $this->registerArchiver($archiver1);
        $this->registerArchiver($archiver2);

        $event->getJobExecution()->willReturn($jobExecution);

        $archiver1->supports($jobExecution)->willReturn(true);
        $archiver2->supports($jobExecution)->willReturn(false);

        $archiver1->archive($jobExecution)->shouldBeCalled();
        $archiver2->archive($jobExecution)->shouldNotBeCalled();

        $this->beforeStatusUpgrade($event);
    }

    function it_tells_if_there_are_at_least_two_archives_for_a_job_execution(
        JobExecution $jobExecution,
        JobExecution $otherJobExecution,
        ArchiverInterface $archiver1,
        ArchiverInterface $archiver2,
        ArchiverInterface $archiver3
    ) {
        $archiver1->getName()->willReturn('output');
        $this->registerArchiver($archiver1);
        $archiver2->getName()->willReturn('media');
        $this->registerArchiver($archiver2);
        $archiver3->getName()->willReturn('jobs');
        $this->registerArchiver($archiver3);

        $jobExecution->isRunning()->willReturn(false);
        $archiver1->getArchives($jobExecution, true)->shouldBeCalled()->willYield(['file1.csv']);
        $archiver2->getArchives($jobExecution, true)->shouldBeCalled()->willYield([]);
        $archiver3->getArchives($jobExecution, true)->shouldBeCalled()->willYield([]);
        $this->hasAtLeastTwoArchives($jobExecution)->shouldReturn(false);

        $otherJobExecution->isRunning()->willReturn(false);
        $archiver1->getArchives($otherJobExecution, true)->shouldBeCalled()->willYield([]);
        $archiver2->getArchives($otherJobExecution, true)->shouldBeCalled()->willYield(['file1.csv', 'file2.csv']);
        $archiver3->getArchives($otherJobExecution, true)->shouldNotBeCalled();
        $this->hasAtLeastTwoArchives($otherJobExecution)->shouldReturn(true);
    }
}
