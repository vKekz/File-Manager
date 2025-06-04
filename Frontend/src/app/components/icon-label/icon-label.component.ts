import { Component, Input } from "@angular/core";

@Component({
  selector: "app-icon-label",
  imports: [],
  templateUrl: "./icon-label.component.html",
  styleUrl: "./icon-label.component.css",
})
export class IconLabelComponent {
  @Input({ required: true })
  public iconName?: string;

  @Input({ required: true })
  public label?: string;
}
