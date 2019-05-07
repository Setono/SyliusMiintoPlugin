<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Twig;

use function func_get_args;
use function func_num_args;
use Safe\Exceptions\FilesystemException;
use Safe\Exceptions\StreamException;
use function Safe\fopen;
use function Safe\stream_get_contents;
use Symfony\Component\VarDumper\Cloner\ClonerInterface;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\Template;
use Twig\TwigFunction;

/**
 * This is a copy of Symfonys dump, except it works in production
 */
final class DumpExtension extends AbstractExtension
{
    private $cloner;
    private $dumper;

    public function __construct(ClonerInterface $cloner, HtmlDumper $dumper = null)
    {
        $this->cloner = $cloner;
        $this->dumper = $dumper;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('setono_sylius_miinto_dump', [$this, 'dump'], ['is_safe' => ['html'], 'needs_context' => true, 'needs_environment' => true]),
        ];
    }

    /**
     * @param Environment $env
     * @param mixed $context
     *
     * @return string
     *
     * @throws FilesystemException
     * @throws StreamException
     */
    public function dump(Environment $env, $context): string
    {
        if (2 === func_num_args()) {
            $vars = [];
            foreach ($context as $key => $value) {
                if (!$value instanceof Template) {
                    $vars[$key] = $value;
                }
            }

            $vars = [$vars];
        } else {
            $vars = func_get_args();
            unset($vars[0], $vars[1]);
        }

        $dump = fopen('php://memory', 'r+b');
        $this->dumper = $this->dumper ?: new HtmlDumper();
        $this->dumper->setCharset($env->getCharset());

        foreach ($vars as $value) {
            $this->dumper->dump($this->cloner->cloneVar($value), $dump);
        }

        return stream_get_contents($dump, -1, 0);
    }
}
