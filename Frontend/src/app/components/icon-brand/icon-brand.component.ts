import { Component, Input } from "@angular/core";
import { IconComponent } from "../icon/icon.component";

@Component({
  selector: "app-icon-brand",
  imports: [IconComponent],
  templateUrl: "./icon-brand.component.html",
  styleUrl: "./icon-brand.component.css",
})
export class IconBrandComponent {
  @Input()
  public width: number = 256;

  @Input()
  public height: number = 256;

  /**
   * Given as rem.
   */
  @Input()
  public brandSize: number = 2;

  @Input()
  public animate: boolean = false;
}
